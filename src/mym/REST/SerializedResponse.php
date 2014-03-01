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

  /**
   * @var Serializer
   */
  private $serializer;

  public function getSerializer()
  {
    if (!$this->serializer) {

      $this->serializer = new Serializer();
      $this->serializer->setFormat($this->format);
      $this->serializer->setJsonOptions($this->jsonOptions);

      if ($this->cacheDir) {
        $this->serializer->setCacheDir($this->cacheDir);
      }
    }

    return $this->serializer;
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
    $this->content = $this->getSerializer()->serialize($this->data);
  }

  public function setData($data)
  {
    $this->data = $data;
    $this->update();
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

  //</editor-fold>
}