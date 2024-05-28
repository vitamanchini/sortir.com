<?php
namespace App\ENUM;
enum Status: string
{
    case Created = "CREATED";
    case Open = "OPEN";
    case Closed = "CLOSED";
    case Ongoing = "ONGOING";
    case Finished = "FINISHED";
    case Cancelled = "CANCELLED";
}
