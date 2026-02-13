<?php
namespace App\Enums;

trait EnumToArray
{

  public static function names(): array
  {
    return array_column(self::cases(), 'name');
  }

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public static function array(): array
  {
    return array_combine(self::values(), self::names());
  }

  public static function customValues() : array
  {
    $customVal = [];

    foreach(static::array() as $k => $v) {
        $customVal[] = [
            'label' => $v,
            'value' => $k
        ];
    }

    return $customVal;
  }
}


enum CrateType : int
{
    use EnumToArray;
    
    case METRIC   = 1;
    case IMPERIAL = 2;

    public static function array(): array
    {
      return array_combine(self::values(), self::names());
    }
}