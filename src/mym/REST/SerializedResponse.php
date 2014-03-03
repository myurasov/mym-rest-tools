<?php

/**
 * Serialized rsponse
 * @copyright 2013-2014, Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Symfony\Component\HttpFoundation\Response;

class SerializedResponse extends Response
{
  private $format = 'json';
  private $cacheDir = false;
  private $jsonOptions = null;
  private $data;

  public function createSerializer()
  {
    $serializer = new Serializer();
    $serializer->setFormat($this->format);
    $serializer->setJsonOptions($this->jsonOptions);

    if ($this->cacheDir) {
      $serializer->setCacheDir($this->cacheDir);
    }

    return $serializer;
  }

  private function update()
  {
    // headers

    if ('json' === $this->format) {

      $this->headers->set('Content-type', 'application/json');

    } else if ('xml' === $this->format) {

      $this->headers->set('Content-type', 'application/xml');

    } else {
      throw new \Exception("Format '$this->format' is not supported");
    }

    // content
    $this->content = $this->createSerializer()->serialize($this->data);
  }

  public function send()
  {
    $this->update();
    return parent::send();
  }

  //<editor-fold desc="accessors">

  public function setCacheDir($cacheDir)
  {
    $this->cacheDir = $cacheDir;
  }

  public function getCacheDir()
  {
    return $this->cacheDir;
  }

  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }

  public function setJsonOptions($jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
  }

  public function getFormat()
  {
    return $this->format;
  }

  public function setFormat($format)
  {
    $this->format = $format;
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function getData()
  {
    return $this->data;
  }

  //</editor-fold>
}