<template>
  <div class="container">
    <h1 class="title">{{ service.name }}</h1>

    <div class="weekdays">
      <button v-for="(slots, date) in slots" :key="date" @click="selectDate(date)"
        :class="['day-btn', { selected: selectedDate === date }]">
        {{ new Date(date).toLocaleDateString('ru-RU', { weekday: 'short', day: 'numeric' }) }}
      </button>
    </div>

    <div v-if="selectedDate" class="slots-grid">
      <button v-for="slot in slots[selectedDate]" :key="slot.time" :disabled="slot.busy" @click="selectSlot(slot)"
        :class="['slot', { 'slot-selected': selectedSlot === slot, 'slot-busy': slot.busy }]">
        {{ slot.time }} <span v-if="slot.busy">(Занято)</span>
      </button>
    </div>

    <div v-if="selectedSlot" class="booking-form">
      <h2>Бронирование на {{ selectedDate }} в {{ selectedSlot.time }}</h2>

      <form @submit.prevent="bookSlot">
        <input v-model="form.client_name" placeholder="Имя" />
        <input v-model="form.client_phone" placeholder="Телефон" />
        <button type="submit" :disabled="form.processing">Забронировать</button>
      </form>
    </div>

    <!-- Модальные окна для ошибок и успеха -->
    <div v-if="flashError" class="modal-overlay" @click="flashError = null">
      <div class="modal-content error-modal" @click.stop>
        <p>{{ flashError }}</p>
        <button @click="flashError = null">Закрыть</button>
      </div>
    </div>


    <div v-if="showSuccessModal" class="success-modal-overlay">
      <div class="modal-content success-modal">
        <h2>Успех!</h2>
        <p>{{ successMessage }}</p>
        <small>Перенаправляю на главную...</small>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import { route } from 'ziggy-js'
import { Ziggy } from './../ziggy'

const props = defineProps({
  service: Object,
  slots: Object,
})

const selectedDate = ref(null)
const selectedSlot = ref(null)
const showSuccessModal = ref(false)
const successMessage = ref('')
const flashError = ref(null)

const page = usePage()
const form = useForm({
  date: '',
  time: '',
  client_name: '',
  client_phone: ''
})

function selectDate(date) {
  selectedDate.value = date
  selectedSlot.value = null
}

function selectSlot(slot) {
  if (!slot.busy) selectedSlot.value = slot
}

function bookSlot() {
  if (!selectedSlot.value) return

  form.date = selectedDate.value
  form.time = selectedSlot.value.time

  const url = route('bookings.store', { service: props.service.id }, false, Ziggy)
  if (!url) return console.error('Route "bookings.store" не найдена!')

  form.post(url, {
    onSuccess: (page) => {
      if (page.props.flash?.success) {
        successMessage.value = page.props.flash.success
        showSuccessModal.value = true
        setTimeout(() => {
          showSuccessModal.value = false
          Inertia.visit(route('services.index'))
        }, 2000)
      }
    },
    onError: (errors) => {
      flashError.value = page.props.errors.error || 'Ошибка при бронировании'
    },
  })
}
</script>

<style scoped>
.container {
  padding: 20px;
  font-family: sans-serif;
}

.title {
  font-size: 22px;
  margin-bottom: 1rem;
}

.weekdays {
  display: flex;
  gap: 8px;
  margin-bottom: 10px;
}

.day-btn {
  padding: 6px 12px;
  border: 1px solid #ccc;
  cursor: pointer;
}

.day-btn.selected {
  background: #3b82f6;
  color: white;
}

.slots-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 16px;
}

.slot {
  border: 1px solid #ccc;
  padding: 8px 12px;
}

.slot-selected {
  background: #10b981;
  color: white;
}

.slot-busy {
  background: #ef4444;
  color: white;
  cursor: not-allowed;
}


.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.error-modal {
  background: #ef4444;
  color: white;
  padding: 20px 30px;
  border-radius: 10px;
  max-width: 300px;
  width: 90%;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.success-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  /* затемнённый фон */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.success-modal {
  background: #10b981;
  color: white;
  padding: 25px 35px;
  border-radius: 10px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}
</style>
