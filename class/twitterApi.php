<?php

namespace vsteffen;
use Abraham\TwitterOAuth\TwitterOAuth;


class twitterApi {

  private $consumer_key;
  private $consumer_secret;
  private $oauth;

  public function __construct($consumer_key, $consumer_secret) {
    $this->consumer_key = $consumer_key;
    $this->consumer_secret = $consumer_secret;
  }

  //      -----------------  REQUEST TOKEN AND AUTHENTICATE APP ----------------
  public function getAuthenticateUrl($callback_url) {
    $this->oauth = new TwitterOAuth($this->consumer_key, $this->consumer_secret);

    $response = $this->oauth->oauth('oauth/request_token', ['oauth_callback' => $callback_url]);
    $_SESSION['oauth_token'] = $response['oauth_token'];
    $_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];

    return $this->oauth->url('oauth/authenticate', ['oauth_token' => $response['oauth_token']]);
  }

  public function getAccessToken($oauth_token, $oauth_verifier) {
    if ($oauth_token != $_SESSION['oauth_token']) {
      throw new Exception('OauthToken mismatch!');
    }
    else {
      $this->oauth = new TwitterOAuth(
        $this->consumer_key,
        $this->consumer_secret,
        $_SESSION['oauth_token'],
        $_SESSION['oauth_token_secret']
      );
      $response = $this->oauth->oauth('oauth/access_token', ['oauth_verifier' => $oauth_verifier]);
      return $response;
    }
  }


  public function verifyCredentials($token, $secret) {
    $this->oauth = new TwitterOAuth(
      $this->consumer_key,
      $this->consumer_secret,
      $token,
      $secret
    );
    return $this->oauth->get('account/verify_credentials', ['skip_status' => true], ['include_entities' => false]);
  }


  public function getLastResult() {
    if (!empty($this->oauth)) {
      return ($this->oauth->getLastHttpCode());
    }
    return 0;
  }

  public function uploadMedia($path) {
    $result = $this->oauth->upload('media/upload', ['media' => '' . $path . '']);
    return $result;
  }

  public function sendTweet($parameters) {
    $result = $this->oauth->post('statuses/update', $parameters);
    return $result;
  }

}


?>
