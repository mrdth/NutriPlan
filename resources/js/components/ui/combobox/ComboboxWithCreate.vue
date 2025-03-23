<template>
  <div class="relative" ref="containerRef">
    <Input
      v-model="search"
      type="search"
      :placeholder="placeholder"
      class="w-full"
      @focus="open = true"
      @keydown.enter.prevent="handleEnter"
    />

    <div v-if="open" class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg">
      <ul class="max-h-60 overflow-auto py-1 text-base sm:text-sm" role="listbox">
        <li
          v-if="showCreateOption"
          class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-100 flex items-center"
          @click="createNewOption"
        >
          <PlusIcon class="mr-2 h-4 w-4" />
          Create "{{ search }}"
        </li>
        <li
          v-for="option in filteredOptions"
          :key="option.id"
          class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-100"
          :class="{ 'bg-gray-100': modelValue === option.id }"
          @click="selectOption(option.id)"
        >
          {{ option.name }}
        </li>
        <li v-if="filteredOptions.length === 0 && !showCreateOption" class="relative py-2 pl-3 pr-9 text-gray-500">
          No results found
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { onClickOutside } from '@vueuse/core'
import { PlusIcon } from 'lucide-vue-next'
import axios from 'axios'

interface Props {
  modelValue: number
  options: Array<{ id: number; name: string }>
  placeholder?: string
  selected?: { id: number; name: string }
  allowCreate?: boolean
  createEndpoint?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search...',
  selected: undefined,
  allowCreate: false,
  createEndpoint: '/ingredients',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void
  (e: 'option-created', option: { id: number; name: string }): void
}>()

const search = ref(props.selected?.name || '')
const open = ref(false)
const containerRef = ref(null)
const isCreating = ref(false)

onClickOutside(containerRef, () => {
  open.value = false
})

watch(() => props.selected, (newValue) => {
  if (newValue) {
    search.value = newValue.name
  }
})

const filteredOptions = computed(() => {
  if (!search.value) return props.options
  const searchLower = search.value.toLowerCase()
  return props.options.filter(option => option.name.toLowerCase().includes(searchLower))
})

const showCreateOption = computed(() => {
  if (!props.allowCreate || !search.value || isCreating.value) return false
  
  // Only show create option if there's no exact match
  const exactMatch = props.options.some(option => 
    option.name.toLowerCase() === search.value.toLowerCase()
  )
  
  return !exactMatch
})

const selectOption = (id: number) => {
  emit('update:modelValue', id)
  open.value = false
  const option = props.options.find(o => o.id === id)
  search.value = option?.name || ''
}

const createNewOption = async () => {
  if (!search.value.trim() || isCreating.value) return
  
  isCreating.value = true
  
  try {
    const response = await axios.post(props.createEndpoint, {
      name: search.value.trim()
    })
    
    const newOption = {
      id: response.data.id,
      name: response.data.name
    }
    
    emit('option-created', newOption)
    emit('update:modelValue', newOption.id)
    open.value = false
  } catch (error) {
    console.error('Failed to create new option:', error)
  } finally {
    isCreating.value = false
  }
}

const handleEnter = () => {
  if (showCreateOption.value) {
    createNewOption()
  } else if (filteredOptions.value.length === 1) {
    selectOption(filteredOptions.value[0].id)
  }
}
</script>
