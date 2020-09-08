<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const NBUSERS = '';
    public const NBMEALS = '';
    public const NBCOMMANDS = '';
    public const NBTAGS = '';
    public const TABUSERS = [];
    public const TABPROVIDERS = [];
    public const TABIMAGES = [];
    public const TABMEALS = [];

    public function load(ObjectManager $manager)
    {
    }
}
