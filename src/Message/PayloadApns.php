<?php

namespace LaravelFCM\Message;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class PayloadApns.
 */
class PayloadApns implements Arrayable
{
    /**
     * @internal
     *
     * @var array
     */
    protected $data;

    /**
     * PayloadData constructor.
     *
     * @param PayloadApnsBuilder $builder
     */
    public function __construct(PayloadApnsBuilder $builder)
    {
        $this->data = $builder->getData();
    }

    /**
     * Transform payloadData to array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
