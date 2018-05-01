<?php


class SweetToothUser
{
    private $prefix = "/user";
    private $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function get($id) {
        if (!is_null($id)) {
            $result = $this->client->get($this->prefix . '/' . $id);
            return $this->client->prepareResponse($result['user']);
        } else {
            $result = $this->client->get($this->prefix);
            return $this->client->prepareResponse($result['users']);
        }      
    }
     
    public function search($filters = null) {
        $result = $this->client->get($this->prefix . '/' . 'search', $filters);
        return $this->client->prepareResponse($result['users']);
    }
     
    public function searchOne($filters) {
        $result = $this->client->get($this->prefix . '/' . 'search', $filters);
        $resultsArray =  $this->client->prepareResponse($result['users']);
  
        if (count($resultsArray) > 0) {
            return $resultsArray[0];
        }
     
        return null;
    }

    /**
     * Creates a user which is a customer of a store.
     * 
     * @param  array              $fields User creation data
     * @return array/json/object          Response body, defaults to array
     */
    public function create($fields){
        return $this->client->post($this->prefix, $fields);
    }

    /**
     * Updates user credentials with the new data contained in $fields.
     * 
     * @param  string              $id      Stores the ID of the user to be updated
     * @param  array               $fields  Contains the new data the user should be updated with
     * @return array/json/object            Response body, defaults to array
     */
    public function update($id, $fields){
        return $this->client->put($this->prefix . '/' . $fields["id"], $fields);
    }

    /**
     * Deletes user  
     *
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function delete($fields){
        $result = $this->client->delete($this->prefix . '/' .$fields['id']);
        return $this->client->prepareResponse($result);
    }

    /**
     * Cleans up memory used when working with User objects.
     */
    public function __destruct(){
        unset($this->prefix);
        unset($this->array);
    }
}
