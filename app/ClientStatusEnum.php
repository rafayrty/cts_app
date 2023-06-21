<?php

namespace App;

enum ClientStatusEnum: string
{
    case NEW_ORDER = 'new order';
    case STARTING = 'starting printing';
    case PACKAGING = 'packaging';
    case READY_FOR_DELIVERY = 'ready for delivery';
    case IN_DELIVERY = 'in delivery';
    case DONE = 'done';
    case STUCK = 'stuck';
    case CANCEL = 'cancel';
}
