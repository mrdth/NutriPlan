<template>
  <div class="relative">
    <Input
      v-model="search"
      type="search"
      :placeholder="placeholder"
      class="w-full"
      @focus="open = true"
    />

    <div v-if="open" class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg">
      <ul class="max-h-60 overflow-auto py-1 text-base sm:text-sm" role="listbox">
        <li
          v-for="option in filteredOptions"
          :key="option.id"
          class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-100"
          :class="{ 'bg-gray-100': modelValue === option.id }"
          @click="selectOption(option.id)"
        >
          {{ option.name }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Input } from '@/components/ui/input'
import { onClickOutside } from '@vueuse/core'

interface Props {
  modelValue: number
  options: Array<{ id: number; name: string }>
  placeholder?: string
  selected?: { id: number; name: string }
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search...',
  selected: undefined,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void
}>()

const search = ref(props.options.find(o => o.id === props.modelValue)?.name || '')
const open = ref(false)
const containerRef = ref(null)

onClickOutside(containerRef, () => {
  open.value = false
})

const filteredOptions = computed(() => {
  if (!search.value) return props.options
  const searchLower = search.value.toLowerCase()
  return props.options.filter(option => option.name.toLowerCase().includes(searchLower))
})

const selectOption = (id: number) => {
  emit('update:modelValue', id)
  open.value = false
  const option = props.options.find(o => o.id === id)
  search.value = option?.name || ''
}
</script>
