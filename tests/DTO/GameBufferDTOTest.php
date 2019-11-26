<?php

namespace App\DTO;

use PHPUnit\Framework\TestCase;

class GameBufferDTOTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     *
     * @param array $data
     */
    public function testDTOCreate($data)
    {
        $dto = new GameBufferDTO($data);
        $this->assertSame('Лига чемпионов УЕФА', $dto->getLeague());
        $this->assertSame('русский', $dto->getLanguage());
        $this->assertSame('Футбол', $dto->getSport());
        $this->assertSame('Реал', $dto->getTeam1());
        $this->assertSame('Барселона', $dto->getTeam2());
        $this->assertSame('sportdata.com', $dto->getSource());
    }

    /**
     * @dataProvider additionProvider
     * @dataProvider additionProviderTeamNotEqualsTeams
     *
     * @param array $data
     */
    public function testNotEqualsTeams($data)
    {
        $dto = new GameBufferDTO($data);
        $this->assertNotSame($dto->getTeam1(), $dto->getTeam2());
    }

    /**
     * @dataProvider additionProviderTeamEqualsTeams
     *
     * @param array $data
     */
    public function testEqualsTeams($data)
    {
        $dto = new GameBufferDTO($data);
        $this->assertSame($dto->getTeam1(), $dto->getTeam2());
    }

    /**
     * @dataProvider additionProviderDate
     *
     * @param mixed $expected
     * @param array $data
     */
    public function testDate($expected, $data)
    {
        $dto = new GameBufferDTO($data);
        $this->assertEquals($expected, $dto->getDate());
    }

    public function additionProvider()
    {
        return [
            [[
                'league' => 'Лига чемпионов УЕФА',
                'lang' => 'русский',
                'sport' => 'Футбол',
                'team1' => 'Реал',
                'team2' => 'Барселона',
                'date' => '2019-01-01 10:01:01',
                'source' => 'sportdata.com',
            ]],
            [[
                'league' => '  Лига чемпионов УЕФА ',
                'lang' => ' русский   ',
                'sport' => '  Футбол
                ',
                'team1' => 'Реал ',
                'team2' => '   Барселона  ',
                'date' => ' 2019-01-01 10:01:01   ',
                'source' => ' sportdata.com',
            ]]
        ];
    }

    public function additionProviderTeamNotEqualsTeams()
    {
        return [
            [[
                'team1' => 'Реал',
                'team2' => 'Реал',
            ]],
            [[
                'team1' => 'реал',
                'team2' => 'Реал',
            ]],
            [[
                'team1' => ' Реал',
                'team2' => 'реал ',
            ]]
        ];
    }

    public function additionProviderTeamEqualsTeams()
    {
        return [
            [[
                'team1' => '',
                'team2' => '',
            ]],
            [[
                'team1' => ' ',
                'team2' => '   ',
            ]],
            [[]]
        ];
    }

    public function additionProviderDate()
    {
        return [
            [new \DateTime('2019-01-01 10:01:01'), ['date' => '2019-01-01 10:01:01']],
            [new \DateTime('2019-01-01 10:01:01'), ['date' => '2019-01-01   10:01:01']],
            [null, ['date' => '']],
            [null, ['date' => ' ']],
            [null, ['date' => '-']],
        ];
    }
}
