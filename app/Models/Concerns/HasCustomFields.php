<?php

namespace App\Models\Concerns;

use App\Models\CustomFieldDefinition;
use App\Models\CustomFieldValue;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFields
{
    /**
     * Get all custom field values for this model.
     */
    public function customFieldValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, null, 'entity_type', 'entity_id');
    }

    /**
     * Get the value of a custom field by its field name.
     */
    public function getCustomField(string $fieldName): mixed
    {
        $value = $this->customFieldValues()
            ->whereHas('definition', fn ($q) => $q->where('field_name', $fieldName))
            ->first();

        return $value?->value;
    }

    /**
     * Set the value of a custom field by its field name.
     */
    public function setCustomField(string $fieldName, mixed $value): void
    {
        $definition = CustomFieldDefinition::where('entity_type', get_class($this))
            ->where('field_name', $fieldName)
            ->firstOrFail();

        $this->customFieldValues()->updateOrCreate(
            [
                'custom_field_definition_id' => $definition->id,
                'entity_type' => get_class($this),
                'entity_id' => $this->id,
            ],
            ['value' => $value]
        );
    }
}
