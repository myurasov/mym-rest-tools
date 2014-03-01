<?php

/**
 * Serializer service
 * @copyright 2013-2014, Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer as JMSSerializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\XmlSerializationVisitor;

class Serializer
{
  /**
   * @var JMSSerializer
   */
  private $serializer;

  private $format = 'json';
  private $cacheDir;
  private $debug = false;
  private $jsonOptions = null;

  private function createSerializer()
  {
    $sb = SerializerBuilder::create();

    // set format
    if ('json' === $this->format) {

        $sv =  new JsonSerializationVisitor(
          /* save original name or annotated one  */
          new SerializedNameAnnotationStrategy(
            new IdenticalPropertyNamingStrategy()
          )
        );

      $sv->setOptions($this->jsonOptions);
      $sb->setSerializationVisitor("json", $sv);

    } else if ('xml' === $this->format) {

      $sb->setSerializationVisitor("xml", new XmlSerializationVisitor(
        /* save original name or annotated one  */
        new SerializedNameAnnotationStrategy(
          new IdenticalPropertyNamingStrategy()
        )
      ));
    }

    // chache dir
    if ($this->cacheDir) {
      $sb->setCacheDir($this->cacheDir);
    }

    // debugging
    $sb->setDebug($this->debug);

    $this->serializer = $sb->build();
  }

  public function serialize($data)
  {
    return $this->getSerializer()->serialize($data, $this->getFormat());
  }

  public function getSerializer()
  {
    if (!$this->serializer) {
      $this->createSerializer();
    }

    return $this->serializer;
  }

  public function setSerializer($serializer)
  {
    $this->serializer = $serializer;
  }

  public function getFormat()
  {
    return $this->format;
  }

  public function setFormat($format)
  {
    $this->format = $format;
  }

  public function getCacheDir()
  {
    return $this->cacheDir;
  }

  public function setCacheDir($cacheDir)
  {
    $this->cacheDir = $cacheDir;
  }

  public function getDebug()
  {
    return $this->debug;
  }

  public function setDebug($debug)
  {
    $this->debug = $debug;
  }

  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }

  public function setJsonOptions($jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
  }
}