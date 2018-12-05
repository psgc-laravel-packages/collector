<?php
namespace PsgcLaravelPackages\Collector;

interface Collectable
{
    public static function collector(?string $dbConnection) : Collector;
    public static function filterQuery(&$query,$filters);
    public static function searchQuery(&$query,$filters);
    public static function sortQuery(&$query,$filters);

}
