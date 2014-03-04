<?php

/**
 * Serializer service helper for Symfony Console applications
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\REST;

use Symfony\Component\Console\Helper\Helper;

class SerializerHelper extends Helper
{
  /**
   * @var Serializer
   */
  protected $serializer;

  public function getName()
  {
    return 'serializer';
  }

  public function __construct(Serializer $serializer)
  {
    $this->serializer = $serializer;
  }

  public function getSerializer()
  {
    return $this->serializer;
  }
}