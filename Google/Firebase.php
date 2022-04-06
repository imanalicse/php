<?php
namespace App\Google;

use App\DotEnv;
use GuzzleHttp\Client;

class Firebase
{
    public function __construct()
    {
        (new DotEnv(__DIR__ . '/.env'))->load();
    }

    protected function getFireBaseDomainUriPrefix() : string {
        return getenv('FIREBASE_DOMAIN_URI_PREFIX'); // "eventbookings.page.link";
    }
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function createFirebaseDynamicLink ($longUrl) : array {
        $key =  getenv('FIREBASE_WEB_API_KEY');
        $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $key;
        $data = [
             "dynamicLinkInfo" => [
                "domainUriPrefix" => $this->getFireBaseDomainUriPrefix(),
                "link" => $longUrl,
                "androidInfo" => [
                   "androidPackageName" => "com.eventbookings.android", // apn=com.eventbookings.android
                   //"androidFallbackLink" => "https://www.eventbookings.com/",
                   //"androidMinPackageVersionCode" => "1",
                ],
                "iosInfo" => [
                    "iosBundleId" => "com.eventbookings.app", //ibi=com.eventbookings.app
                ]
             ],
             "suffix" => [
                "option" => "UNGUESSABLE" //SHORT or UNGUESSABLE
             ]
        ];

        $http = new Client();
        $response = $http->post($url, [
            'body' => json_encode($data),
            'headers' => ['X-Requested-With' => 'XMLHttpRequest', 'Content-Type' => 'application/json']
        ]);

        $parse_response = json_decode($response->getBody()->getContents(), true);
        $return_response = [
            'is_success' => true,
            'message' => '',
            'shortLink' => '',
        ];
        if ($response->getStatusCode() == 200) {
            $shortLink = $parse_response["shortLink"];
            $return_response['shortLink'] = $shortLink;
        }
        else {
            $message = $parse_response["error"]["message"];
            throw new Exception("Unable to create short link: ". $message);
        }

        return $return_response;
   }

   /**
    * $firebase = new Firebase();
       $short_links = $firebase->getFirebaseShortLink("https://olive.doyour.events/b/event/test-speaker-event");
    */
   public function getFirebaseShortLink($longLink) : string {
        $firebase_response = $this->createFirebaseDynamicLink($longLink);
        return $firebase_response["shortLink"];
   }
}