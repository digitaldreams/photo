<?php

namespace Photo\Services;

use GuzzleHttp\Client;

class HereReverseGeocoding
{
    private $lat;
    private $lng;
    /**
     * @var
     */
    protected $response;
    /**
     * @var
     */
    protected $client;

    protected $data = [];

    /**
     * HereReverseGeocoding constructor.
     *
     * @param $lat
     * @param $lng
     */
    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->client = new Client();
    }

    public function fetch()
    {
        $response = $this->client->request('GET', 'https://reverse.geocoder.api.here.com/6.2/reversegeocode.json', ['query' => [
            'prox' => "$this->lat,$this->lng,250",
            'mode' => 'retrieveAddresses',
            'maxresults' => 1,
            'gen' => 9,
            'app_id' => config('photo.here.app_id'),
            'app_code' => config('photo.here.app_code'),
        ]]);
        $this->response = json_decode($response->getBody()->getContents(), true);
        $this->parse();

        return $this;
    }

    public function response()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    private function parse()
    {
        $response = $this->response['Response']['View'][0]['Result'][0] ?? [];
        if (isset($response['Location'])) {
            $location = $response['Location'];
            $this->data['place_id'] = $location['LocationId'] ?? null;
            $this->data['address'] = $location['Address']['Street'] ?? null;
            $this->data['locality'] = $location['Address']['District'] ?? null;
            $this->data['city'] = $location['Address']['City'] ?? null;
            $this->data['state'] = $location['Address']['State'] ?? null;
            $this->data['country'] = $location['Address']['Country'] ?? null;
        }

        return $this->data;
    }

    public function toArray()
    {
        return $this->data;
    }
}
