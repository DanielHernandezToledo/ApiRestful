<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'identificador' => (int) $product->id,
            'titulo' => (string) $product->name,
            'detalles' => (string) $product->description,
            'disponibles' => (string) $product->quantity,
            'estado' => (string) $product->status,
            'imagen' => url("{$product->image}"),
            'vendedor' => (int) $product->seller_id,
            'fechaCreacion' => (string) $product->created_at,
            'fechaActualizacion' => (string) $product->updated_at,
            'fechaEliminacion' => isset($product->deleted_at ) ? (string) $product->deleted_at : null
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identificador' => 'id',
            'titulo' => 'name',
            'detalles' => 'description',
            'disponibles' => 'quantity',
            'estado' => 'status',
            'imagen' => 'image',
            'vendedor' => 'seller_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}