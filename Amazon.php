<?php

require_once('HTTPClient.php');

class Amazon {
    private $public_key  = '';
    private $private_key = '';
    private $region = 'com';
    private $http;

    public $partner = array(
        'de'  => 'splitbrain-21',
        'com' => 'splitbrain-20',
    );

    function __construct($public,$private){
        $this->public_key  = $public;
        $this->private_key = $private;
        $this->http = new HTTPClient();
    }

    public function setRegion($region){
        $this->region      = $region;
    }

    public function search($query){

        $opts = array();
        $opts['Operation']     = 'ItemSearch';
        $opts['Keywords']      = $query;
        $opts['SearchIndex']   = 'All';
        $opts['ResponseGroup'] = 'Medium';

        if(isset($this->partner[$this->region])){
            $opts['AssociateTag'] = $this->partner[$this->region];
        }

        // support paged results
        $result = array();
        $pages = 1;
        for($page=1; $page <= $pages; $page++){
            $opts['ProductPage'] = $page;

            $url = $this->signedRequestURI($opts);
            $res = $this->http->get($url);
            if(!$res) return false;

            $xml   = new SimpleXMLElement($res);
            $pages = $xml->Items->TotalPages;

            foreach($xml->Items->Item as $item){
                $result[] = $item;
            }
        }

        return $result;
    }

   /**
    * Create a signed Request URI
    *
    * Original copyright notice:
    *
    * Copyright (c) 2009 Ulrich Mierendorff
    *
    * Permission is hereby granted, free of charge, to any person obtaining a
    * copy of this software and associated documentation files (the "Software"),
    * to deal in the Software without restriction, including without limitation
    * the rights to use, copy, modify, merge, publish, distribute, sublicense,
    * and/or sell copies of the Software, and to permit persons to whom the
    * Software is furnished to do so, subject to the following conditions:
    *
    * The above copyright notice and this permission notice shall be included in
    * all copies or substantial portions of the Software.
    *
    * @author Ulrich Mierendorff <ulrich.mierendorff@gmx.net>
    * @link http://mierendo.com/software/aws_signed_query/
    */
    private function signedRequestURI($params){
        $method = "GET";
        $host = "ecs.amazonaws.".$this->region;
        $uri = "/onca/xml";

        // additional parameters
        $params["Service"] = "AWSECommerceService";
        $params["AWSAccessKeyId"] = $this->public_key;
        // GMT timestamp
        $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
        // API version
        $params["Version"] = "2009-11-01";

        // sort the parameters
        ksort($params);

        // create the canonicalized query
        $canonicalized_query = array();
        foreach ($params as $param=>$value)
        {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $canonicalized_query[] = $param."=".$value;
        }
        $canonicalized_query = implode("&", $canonicalized_query);

        // create the string to sign
        $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;

        // calculate HMAC with SHA256 and base64-encoding
        if(function_exists('hash_hmac')){
            $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->private_key, true));
        }elseif(function_exists('mhash')){
            $signature = base64_encode(mhash(MHASH_SHA256, $string_to_sign, $this->private_key));
        }else{
            die('missing crypto function, can\'t sign request');
        }

        // encode the signature for the request
        $signature = str_replace("%7E", "~", rawurlencode($signature));

        // create request
        return "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;
    }

}
