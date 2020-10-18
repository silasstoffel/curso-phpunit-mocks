<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;


class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $fiat147 = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $variant = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
        ->willReturn([$fiat147,$variant]);

        $leilaoDao->method('recuperarFinalizados')
        ->willReturn([$fiat147,$variant]);

        $leilaoDao->expects(
            $this->exactly(2)
        )
        ->method('atualiza')
        ->withConsecutive(
            [$fiat147],
            [$variant]
        );

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        $leiloes = [$fiat147, $variant];
        static::assertCount(2, $leiloes);
        static::assertEquals(
            'Fiat 147 0Km',
            $leiloes[0]->recuperarDescricao()
        );
        static::assertEquals(
            'Variante 0Km',
            $leiloes[1]->recuperarDescricao()
        );
    }
}