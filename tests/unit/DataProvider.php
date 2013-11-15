<?php

/*
 * Mikromongo
 */

namespace tests\unit;

use Trismegiste\ZeroKelvin\Serialization;

/**
 * Provider is a collection of provider
 */
trait DataProvider
{

    public function objectProvider()
    {
        $simple = new \stdClass();
        $simple->propInt = 123;
        $simple->propBool = true;
        $simple->propVector = [1, 2, 3];
        $simple->propStr = "do or do not";
        $expected = [
            Serialization::META_CLASS => 'stdClass',
            '+propInt' => 123,
            '+propBool' => true,
            '+propVector' => [1, 2, 3],
            '+propStr' => "do or do not",
            Serialization::META_UUID => 'AAAA',
            '@foreign' => []
        ];

        $object1 = new \tests\fixtures\Access();
        $flat1 = [
            Serialization::META_CLASS => 'tests\fixtures\Access',
            Serialization::META_UUID => 'AAAA',
            'noise' => null,
            '-tests\fixtures\Access-notInherited' => 111,
            'inherited' => 222,
            '+openbar' => 333,
            '@foreign' => []
        ];

        $object2 = new \tests\fixtures\Entity();
        $flat2 = [
            [
                Serialization::META_CLASS => 'tests\fixtures\Entity',
                Serialization::META_UUID => 'AAAA',
                Serialization::META_FOREIGN => ['AAAB', 'AAAC'],
                '-tests\fixtures\Entity-answer' => 42,
                '+now' => ['@ref' => 'AAAB'],
                '-tests\fixtures\Entity-compound' => [1, 4, 9],
                'init' => 5678,
                'embed' => ['@ref' => 'AAAC'],
                '-tests\fixtures\Entity-ref' => ['@ref' => 'AAAC'],
                'custom' => [
                    '@classname' => 'ArrayObject',
                    '@content' => 'x:i:0;a:3:{i:0;i:299;i:1;i:792;i:2;i:458;};m:a:0:{}'
                ]
            ],
            [
                Serialization::META_CLASS => 'DateTime',
                Serialization::META_UUID => 'AAAB',
                '+date' => date('Y-m-d H:i:s'),
                '+timezone_type' => 3,
                '+timezone' => 'EST'
            ],
            [
                Serialization::META_CLASS => 'tests\fixtures\Access',
                Serialization::META_UUID => 'AAAC',
                '-tests\fixtures\Access-notInherited' => 111,
                'noise' => null,
                'inherited' => 222,
                '+openbar' => 333
            ]
        ];

        return [
            [$simple, [$expected]],
            [$object1, [$flat1]],
            [$object2, $flat2]
        ];
    }

    public function getSplType()
    {
        $spl2 = new \SplObjectStorage();
        $spl2[new \stdClass()] = 123;
        $spl2[new \stdClass()] = 456;
        $flt2 = [Serialization::META_CLASS => 'SplObjectStorage', Serialization::META_CUSTOM => 'x:i:2;O:8:"stdClass":0:{},i:123;;O:8:"stdClass":0:{},i:456;;m:a:0:{}'];

        return [
            [$spl2, $flt2]
        ];
    }

}