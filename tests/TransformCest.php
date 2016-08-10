<?php
declare(strict_types = 1);

namespace Test\Functional\Transform;

use Diaclone\Transformer\AbstractTransformer;
use Diaclone\Transformer\IntegerTransformer;
use Diaclone\Transformer\StringTransformer;
use Illuminate\Support\Collection;
use Transform;
use UnitTester;

class TransformCest
{
    public function testTransformationAll(UnitTester $I)
    {
        $friends = new Collection([
            new Person('Paul', 'Real estate novelist'),
            new Person('John', 'Bartender'),
            new Person('Davy', 'Sailor'),
            new Person('Unknown', 'Waitress'),
        ]);
        $output = Transform::transform(new Person('Bill', 'Piano Man', $friends), new PersonTransformer(), 'person');
        $expected = [
            'person' => [
                'name'       => 'My name is Bill',
                'age'        => 42,
                'pigLatin'   => 'Ymay amenay isyay Illbay',
                'occupation' => [
                    'name' => 'Piano Man',
                ],
                'friends'    => [
                    [
                        'name'       => 'My name is Paul',
                        'age'        => 42,
                        'pigLatin'   => 'Ymay amenay isyay Aulpay',
                        'occupation' => [
                            'name' => 'Real estate novelist',
                        ],
                        'friends'    => [],
                    ],
                    [
                        'name'       => 'My name is John',
                        'age'        => 42,
                        'pigLatin'   => 'Ymay amenay isyay Ohnjay',
                        'occupation' => [
                            'name' => 'Bartender',
                        ],
                        'friends'    => [],
                    ],
                    [
                        'name'       => 'My name is Davy',
                        'age'        => 42,
                        'pigLatin'   => 'Ymay amenay isyay Avyday',
                        'occupation' => [
                            'name' => 'Sailor',
                        ],
                        'friends'    => [],
                    ],
                    [
                        'name'       => 'My name is Unknown',
                        'age'        => 42,
                        'pigLatin'   => 'Ymay amenay isyay Unknownyay',
                        'occupation' => [
                            'name' => 'Waitress',
                        ],
                        'friends'    => [],
                    ],
                ],
            ],
        ];
        $I->assertEquals($expected, $output);
    }
}

class Person
{
    public $my_job;

    protected $friends;
    protected $name;

    public function __construct($name, $occupation, $friends = [])
    {
        $this->friends = $friends;
        $this->name = $name;
        $this->my_job = new Occupation($occupation);
    }

    public function getAge()
    {
        return 42;
    }

    public function getName()
    {
        return 'My name is ' . $this->name;
    }

    public function getNunya()
    {
        return 'secret';
    }

    public function getMyFriends()
    {
        return $this->friends;
    }
}

class PersonTransformer extends AbstractTransformer
{
    protected static $propertyTransformers = [
        'name'       => StringTransformer::class,
        'age'        => IntegerTransformer::class,
        'my_job'     => OccupationTransformer::class,
        'my_friends' => PersonTransformer::class,
        'pigLatin'   => PigLatinTransformer::class,
    ];

    protected static $mappedProperties = [
        'name'       => 'name',
        'age'        => 'age',
        'pigLatin'   => 'pigLatin',
        'my_job'     => 'occupation',
        'my_friends' => 'friends',
    ];
}

class Occupation
{
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}

class OccupationTransformer extends AbstractTransformer
{
    protected static $propertyTransformers = [
        'name' => StringTransformer::class,
    ];

    protected static $mappedProperties = [
        'name' => 'name',
    ];
}

class PigLatinTransformer extends AbstractTransformer
{
    public function transform($data, $property, $key)
    {
        $value = $this->getPropertyValue($data, 'name');
        $parts = explode(' ', $value);
        $converted = [];
        foreach ($parts as $part) {
            $firstLetter = lcfirst($part[0]);
            if (in_array($firstLetter, ['a', 'e', 'i', 'o', 'u',])) {
                $converted[] = $part . 'yay';
            } else {
                $pigged = substr($part, 1) . $firstLetter . 'ay';
                if ($firstLetter !== $part[0]) {
                    $pigged = ucfirst($pigged);
                }

                $converted[] = $pigged;
            }
        }

        return implode(' ', $converted);
    }
}