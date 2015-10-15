<?php

namespace Rx\Operator;

use Rx\ObservableInterface;
use Rx\Observer\AutoDetachObserver;
use Rx\Observer\CallbackObserver;
use Rx\ObserverInterface;
use Rx\SchedulerInterface;

class ConcatOperator implements OperatorInterface {
    /** @var \Rx\ObservableInterface */
    private $subsequentObservable;

    /**
     * Concat constructor.
     * @param ObservableInterface $subsequentObservable
     */
    public function __construct(ObservableInterface $subsequentObservable)
    {
        $this->subsequentObservable = $subsequentObservable;
    }

    /**
     * @inheritDoc
     */
    public function call(
        ObservableInterface $observable,
        ObserverInterface $observer,
        SchedulerInterface $scheduler = null
    ) {
        return $observable->subscribe(new CallbackObserver(
            [$observer, 'onNext'],
            [$observer, 'onError'],
            function () use ($observer) {
                $o = new AutoDetachObserver($observer);
                $o->setDisposable($this->subsequentObservable->subscribe($o));
            }
        ));
    }
}
