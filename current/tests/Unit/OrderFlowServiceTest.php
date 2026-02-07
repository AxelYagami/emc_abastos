<?php

namespace Tests\Unit;

use App\Models\Orden;
use App\Services\OrderFlowService;
use PHPUnit\Framework\TestCase;

class OrderFlowServiceTest extends TestCase
{
    private OrderFlowService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderFlowService();
    }

    public function test_pickup_flow_sequence(): void
    {
        $orden = new Orden();
        $orden->tipo_entrega = 'pickup';
        $orden->fulfillment_type = 'pickup';

        $orden->status = 'nuevo';
        $this->assertEquals('confirmado', $this->service->nextStatus($orden));

        $orden->status = 'confirmado';
        $this->assertEquals('preparando', $this->service->nextStatus($orden));

        $orden->status = 'preparando';
        $this->assertEquals('listo', $this->service->nextStatus($orden));

        $orden->status = 'listo';
        $this->assertEquals('entregado', $this->service->nextStatus($orden));

        $orden->status = 'entregado';
        $this->assertNull($this->service->nextStatus($orden));
    }

    public function test_delivery_flow_sequence(): void
    {
        $orden = new Orden();
        $orden->tipo_entrega = 'delivery';

        $orden->status = 'nuevo';
        $this->assertEquals('confirmado', $this->service->nextStatus($orden));

        $orden->status = 'confirmado';
        $this->assertEquals('preparando', $this->service->nextStatus($orden));

        $orden->status = 'preparando';
        $this->assertEquals('listo', $this->service->nextStatus($orden));

        $orden->status = 'listo';
        $this->assertEquals('en_ruta', $this->service->nextStatus($orden));

        $orden->status = 'en_ruta';
        $this->assertEquals('entregado', $this->service->nextStatus($orden));

        $orden->status = 'entregado';
        $this->assertNull($this->service->nextStatus($orden));
    }

    public function test_cancelled_returns_null(): void
    {
        $orden = new Orden();
        $orden->tipo_entrega = 'pickup';
        $orden->status = 'cancelado';

        $this->assertNull($this->service->nextStatus($orden));
    }

    public function test_needs_repartidor_only_for_delivery_listo(): void
    {
        $orden = new Orden();

        // Pickup listo: no necesita repartidor
        $orden->tipo_entrega = 'pickup';
        $orden->status = 'listo';
        $orden->repartidor_id = null;
        $this->assertFalse($this->service->needsRepartidor($orden));

        // Delivery listo sin repartidor: necesita
        $orden->tipo_entrega = 'delivery';
        $orden->status = 'listo';
        $orden->repartidor_id = null;
        $this->assertTrue($this->service->needsRepartidor($orden));

        // Delivery listo con repartidor: no necesita
        $orden->repartidor_id = 1;
        $this->assertFalse($this->service->needsRepartidor($orden));

        // Delivery pero no en status listo: no necesita
        $orden->repartidor_id = null;
        $orden->status = 'preparando';
        $this->assertFalse($this->service->needsRepartidor($orden));
    }

    public function test_steps_differ_by_tipo_entrega(): void
    {
        $pickup = new Orden();
        $pickup->tipo_entrega = 'pickup';

        $delivery = new Orden();
        $delivery->tipo_entrega = 'delivery';

        $pickupSteps = OrderFlowService::stepsFor($pickup);
        $deliverySteps = OrderFlowService::stepsFor($delivery);

        $this->assertNotContains('en_ruta', $pickupSteps);
        $this->assertContains('en_ruta', $deliverySteps);
        $this->assertCount(5, $pickupSteps);
        $this->assertCount(6, $deliverySteps);
    }

    public function test_status_labels(): void
    {
        $this->assertEquals('Nuevo', OrderFlowService::statusLabel('nuevo'));
        $this->assertEquals('En ruta', OrderFlowService::statusLabel('en_ruta'));
        $this->assertEquals('Entregado', OrderFlowService::statusLabel('entregado'));
        $this->assertEquals('Unknown', OrderFlowService::statusLabel('unknown'));
    }
}
