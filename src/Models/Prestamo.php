<?php

namespace Climaco\Biblioteca\Models;

class Prestamo
{
    private $id;
    private $idUsuario;
    private $idLibro;
    private $fechaPrestamo;
    private $fechaDevolucionEsperada;
    private $fechaDevolucionReal;
    private $estado; // 'activo', 'devuelto', 'vencido'
    private $observaciones;

    public function __construct(int $id, int $idUsuario, int $idLibro, string $fechaPrestamo = null, string $fechaDevolucionEsperada = null, string $observaciones = '')
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLibro = $idLibro;
        $this->fechaPrestamo = $fechaPrestamo ?? date('Y-m-d');
        $this->fechaDevolucionEsperada = $fechaDevolucionEsperada ?? date('Y-m-d', strtotime('+15 days'));
        $this->fechaDevolucionReal = null;
        $this->estado = 'activo';
        $this->observaciones = $observaciones;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getIdLibro(): int
    {
        return $this->idLibro;
    }

    public function getFechaPrestamo(): string
    {
        return $this->fechaPrestamo;
    }

    public function getFechaDevolucionEsperada(): string
    {
        return $this->fechaDevolucionEsperada;
    }

    public function getFechaDevolucionReal(): ?string
    {
        return $this->fechaDevolucionReal;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getObservaciones(): string
    {
        return $this->observaciones;
    }

    public function marcarComoDevuelto(string $fechaDevolucion = null, string $observaciones = ''): void
    {
        $this->fechaDevolucionReal = $fechaDevolucion ?? date('Y-m-d');
        $this->estado = 'devuelto';
        if ($observaciones) {
            $this->observaciones = $observaciones;
        }
    }

    public function marcarComoVencido(): void
    {
        $this->estado = 'vencido';
    }

    public function estaVencido(): bool
    {
        return $this->estado === 'activo' && date('Y-m-d') > $this->fechaDevolucionEsperada;
    }

    public function getDiasRetraso(): int
    {
        if ($this->estado !== 'activo') {
            return 0;
        }

        $fechaActual = new \DateTime();
        $fechaEsperada = new \DateTime($this->fechaDevolucionEsperada);

        if ($fechaActual > $fechaEsperada) {
            return $fechaActual->diff($fechaEsperada)->days;
        }

        return 0;
    }

    public function extenderPrestamo(int $dias): void
    {
        if ($this->estado === 'activo') {
            $nuevaFecha = new \DateTime($this->fechaDevolucionEsperada);
            $nuevaFecha->add(new \DateInterval('P' . $dias . 'D'));
            $this->fechaDevolucionEsperada = $nuevaFecha->format('Y-m-d');
        }
    }
}
