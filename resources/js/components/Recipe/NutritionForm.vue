<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Nutrition Information</h3>
            <Button type="button" variant="ghost" size="sm" @click="isOpen = !isOpen">
                <ChevronDownIcon v-if="isOpen" class="h-4 w-4" />
                <ChevronRightIcon v-else class="h-4 w-4" />
                {{ isOpen ? 'Hide' : 'Show' }}
            </Button>
        </div>

        <div v-if="isOpen" class="space-y-4 rounded-lg border p-4">
            <p class="text-sm text-muted-foreground">All fields are optional. Enter nutrition information according to the schema.org format.</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                <div>
                    <Label for="calories">Calories</Label>
                    <Input id="calories" v-model="nutrition.calories" type="text" class="mt-1" placeholder="e.g. 240 calories" />
                </div>

                <div>
                    <Label for="carbohydrate_content">Carbohydrates</Label>
                    <Input id="carbohydrate_content" v-model="nutrition.carbohydrate_content" type="text" class="mt-1" placeholder="e.g. 37g" />
                </div>

                <div>
                    <Label for="protein_content">Protein</Label>
                    <Input id="protein_content" v-model="nutrition.protein_content" type="text" class="mt-1" placeholder="e.g. 4g" />
                </div>

                <div>
                    <Label for="fat_content">Fat</Label>
                    <Input id="fat_content" v-model="nutrition.fat_content" type="text" class="mt-1" placeholder="e.g. 9g" />
                </div>

                <div>
                    <Label for="fiber_content">Fiber</Label>
                    <Input id="fiber_content" v-model="nutrition.fiber_content" type="text" class="mt-1" placeholder="e.g. 2g" />
                </div>

                <div>
                    <Label for="sugar_content">Sugar</Label>
                    <Input id="sugar_content" v-model="nutrition.sugar_content" type="text" class="mt-1" placeholder="e.g. 5g" />
                </div>

                <div>
                    <Label for="cholesterol_content">Cholesterol</Label>
                    <Input id="cholesterol_content" v-model="nutrition.cholesterol_content" type="text" class="mt-1" placeholder="e.g. 0mg" />
                </div>

                <div>
                    <Label for="sodium_content">Sodium</Label>
                    <Input id="sodium_content" v-model="nutrition.sodium_content" type="text" class="mt-1" placeholder="e.g. 200mg" />
                </div>

                <div>
                    <Label for="saturated_fat_content">Saturated Fat</Label>
                    <Input id="saturated_fat_content" v-model="nutrition.saturated_fat_content" type="text" class="mt-1" placeholder="e.g. 2g" />
                </div>

                <div>
                    <Label for="trans_fat_content">Trans Fat</Label>
                    <Input id="trans_fat_content" v-model="nutrition.trans_fat_content" type="text" class="mt-1" placeholder="e.g. 0g" />
                </div>

                <div>
                    <Label for="unsaturated_fat_content">Unsaturated Fat</Label>
                    <Input id="unsaturated_fat_content" v-model="nutrition.unsaturated_fat_content" type="text" class="mt-1" placeholder="e.g. 7g" />
                </div>

                <div>
                    <Label for="serving_size">Serving Size</Label>
                    <Input id="serving_size" v-model="nutrition.serving_size" type="text" class="mt-1" placeholder="e.g. 1 serving" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ChevronDownIcon, ChevronRightIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue: {
        calories?: string;
        carbohydrate_content?: string;
        cholesterol_content?: string;
        fat_content?: string;
        fiber_content?: string;
        protein_content?: string;
        saturated_fat_content?: string;
        serving_size?: string;
        sodium_content?: string;
        sugar_content?: string;
        trans_fat_content?: string;
        unsaturated_fat_content?: string;
    };
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: typeof props.modelValue): void;
}>();

const isOpen = ref(false);

const nutrition = ref({ ...props.modelValue });

// Watch for changes and emit updates
watch(
    nutrition,
    (newValue) => {
        emit('update:modelValue', newValue);
    },
    { deep: true },
);
</script>
