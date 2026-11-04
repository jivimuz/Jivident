<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('request_no', 40);
            $table->string('requesting_unit', 100);
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'ordered'])->default('draft');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['klienfk', 'request_no']);
            $table->index(['klienfk', 'status']);
        });

        Schema::create('t_procurement_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procurement_request_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('item_name');
            $table->string('unit', 50)->nullable();
            $table->decimal('qty', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('procurement_request_id')->references('id')->on('t_procurement_requests')->cascadeOnDelete();
            $table->index('product_id');
        });

        Schema::create('t_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->unsignedBigInteger('procurement_request_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('po_no', 40);
            $table->date('po_date');
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['klienfk', 'po_no']);
            $table->index(['klienfk', 'status']);
        });

        $receive = Module::where('code', 'RO')->first();
        $groupId = $receive?->group_id ?? 6;
        $groupName = $receive?->group_name ?? 'FM';
        $listNo = (int) Module::where('group_id', $groupId)->where('isheader', 0)->max('list_no') + 1;
        Module::updateOrCreate(['code' => 'PRC'], [
            'name' => 'Procurement',
            'icon' => 'iconoir-box',
            'route' => 'procurement',
            'group_id' => $groupId,
            'group_name' => $groupName,
            'list_no' => $listNo,
            'isheader' => 0,
            'isactive' => 1,
        ]);
    }

    public function down(): void
    {
        Module::where('code', 'PRC')->delete();
        Schema::dropIfExists('t_purchase_orders');
        Schema::dropIfExists('t_procurement_request_items');
        Schema::dropIfExists('t_procurement_requests');
    }
};
