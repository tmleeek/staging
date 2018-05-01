<?php

/**
 * 
 *
 */
class SweetToothStory
{
    
    const STORY_TYPE_ORDER     = 'order';
    const STORY_TYPE_SIGNUP    = 'signup';
    
    private $prefix = "/story";
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function get($id) {
        if (!is_null($id)) {
            $result = $this->client->get($this->prefix . '/' . $id);
            return $this->client->prepareResponse($result['story']);
        } else {
            $result = $this->client->get($this->prefix);
            return $this->client->prepareResponse($result['stories']);            
        }
    }

    /**
     * Creates story (a link between a user and a reward event).
     * 
     * @param  array $fields        Story data
     * @return array/json/object    Response body, array by default
     */
    public function create($fields) {
        return $this->client->post($this->prefix, $fields);
    }

    public function search($filters = null) {
        $result = $this->client->get($this->prefix . '/' . 'search', $filters);
        return $this->client->prepareResponse($result['stories']);
    }
  
    public function searchOne($filters) {
        $result = $this->client->get($this->prefix . '/' . 'search', $filters);
        $resultsArray =  $this->client->prepareResponse($result['stories']);
     
        if (count($resultsArray) > 0) {
            return $resultsArray[0];
        }
     
        return null;
    }    

    /**
     * Cleans up memory used when working with Story objects.
     */
    public function __destruct(){
        unset($this->prefix);
        unset($this->array);
    }
}

