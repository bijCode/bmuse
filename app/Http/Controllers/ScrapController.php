<?php

namespace App\Http\Controllers;

use App\Models\Scrap;
use App\Models\ScrapedData;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ScrapController extends Controller
{

    public function scrapeData()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://jameelcast.pinecast.co/');

        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $scrappedData = $crawler->filter('article')->each(function($node){
                                    $title = $node->filter('a h1')->first()->text();
                                    $paragraphs = $node->filter('p')->eq(0)->each(function($pNode) {
                                        return $pNode->text();
                                    });
                                    $audioUrl = $node->filter('iframe')->first()->attr('src');
                                    // $imageUrl = $node->selectLink('img')->first()->text();
                                        return [
                                            'title'=>$title,
                                            'episodeNotes'=>implode("\n", $paragraphs),
                                            'audioUrl' => $audioUrl,
                                            // 'imageUrl'=>$imageUrl,
                                        ];
                                });
        DB::beginTransaction();

        try {

            $scraping = new Scrap();
            $scraping->save();
            foreach ($scrappedData as $scrapingDataNode) {
                ScrapedData::create([
                    'scrap_id' => $scraping->id,
                    'title'=>$scrapingDataNode['title'],
                    'episode_notes'=>$scrapingDataNode['episodeNotes'],
                    'audio_url'=>$scrapingDataNode['audioUrl'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Data scraped and stored successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to save data'], 500);
        }

    }

    public function scraps(){
        $scraps = Scrap::all(); // Retrieve all saved scraped data
        return response()->json($scraps);
    }
}
