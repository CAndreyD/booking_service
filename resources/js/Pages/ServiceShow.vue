<template>
  <div class="container">
    <h1 class="title">{{ service.name }}</h1>

    <!-- Календарь недели -->
    <div class="weekdays">
      <button v-for="day in weekDays" :key="day.date" @click="selectDate(day.date)"
        :class="['day-btn', { selected: selectedDate === day.date, disabled: day.isSunday }]" :disabled="day.isSunday">
        {{ day.label }}
      </button>
    </div>

    <!-- Слоты -->
    <div v-if="selectedDate && weekSlots[selectedDate]" class="slots-grid">
      <button v-for="slotObj in weekSlots[selectedDate]" :key="slotObj.time" @click="selectSlot(slotObj)" :class="{
        slot: true,
        'slot-selected': selectedSlot === slotObj,
        'slot-busy': slotObj.busy
      }" :disabled="slotObj.busy">
        {{ slotObj.time }}
        <span v-if="slotObj.busy">Занято</span>
      </button>
    </div>

    <div v-else class="no-slots">Нет доступных слотов</div>

    <!-- Форма бронирования -->
    <div v-if="selectedSlot" class="booking-form">
      <h2>Бронирование на {{ selectedDate }} в {{ selectedSlot.time }}</h2>
      <form @submit.prevent="bookSlot">
        <input v-model="clientName" placeholder="Ваше имя" required />
        <input v-model="clientPhone" placeholder="Телефон" required />
        <button type="submit">Забронировать</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({ service: Object })


const selectedDate = ref(null)
const selectedSlot = ref(null)
const clientName = ref('')
const clientPhone = ref('')
const weekDays = [
  { date: '2025-10-13', label: 'Пн 13.10', isSunday: false },
  { date: '2025-10-14', label: 'Вт 14.10', isSunday: false },
  { date: '2025-10-15', label: 'Ср 15.10', isSunday: false },
  { date: '2025-10-16', label: 'Чт 16.10', isSunday: false },
  { date: '2025-10-17', label: 'Пт 17.10', isSunday: false },
  { date: '2025-10-18', label: 'Сб 18.10', isSunday: false },
  { date: '2025-10-18', label: 'Вс 19.10', isSunday: true },
];

const weekSlots = ref({})

function generateWeek(startDateStr) {
  const start = new Date(startDateStr)
  const arr = []
  for (let i = 0; i < 6; i++) { // Пн–Сб
    const d = new Date(start)
    d.setDate(d.getDate() + i)
    const dow = d.getDay()
    if (dow === 0) continue
    arr.push({
      date: d.toISOString().split('T')[0],
      label: d.toLocaleDateString('ru-RU', { weekday: 'short', day: 'numeric', month: 'numeric' }),
      isSunday: dow === 0
    })
  }
  weekDays.value = arr
  selectedDate.value = arr[0]?.date || null
}

async function fetchWeekSlots() {
  const start = weekDays[0].date;
  const end = weekDays[weekDays.length - 1].date;

  const res = await fetch(`/services/${props.service.id}/week-slots?start=${start}&end=${end}`);
  const data = await res.json();
  weekSlots.value = data.slots;
}

function selectDate(date) {
  selectedDate.value = date
  selectedSlot.value = null
}

function selectSlot(slot) {
  if (!slot.busy) {
    selectedSlot.value = slot
  }
}

async function bookSlot() {
  const payload = {
    date: selectedDate.value,
    time: selectedSlot.value.time,
    client_name: clientName.value,
    client_phone: clientPhone.value
  }
  await Inertia.post(`/services/${props.service.id}/bookings`, payload)
}

onMounted(() => {
  const today = new Date()
  generateWeek(today.toISOString().split('T')[0])
  const start = weekDays.value[0].date
  const end = weekDays.value[weekDays.value.length - 1].date
  fetchWeekSlots(start, end)
})
</script>

<style scoped>
.container {
  font-family: sans-serif;
  padding: 16px;
}

.title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 16px;
}

.weekdays {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

.day-btn {
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  background: #fff;
}

.day-btn.selected {
  background: #3b82f6;
  color: #fff;
  border-color: #2563eb;
}

.slots-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

.slot {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  background: #fff;
}

.slot-selected {
  background-color: #10b981;
  color: #fff;
}

.slot-busy {
  background-color: #ef4444;
  color: #fff;
  cursor: not-allowed;
}

.booking-form {
  margin-top: 16px;
}

.booking-form input {
  padding: 6px 10px;
  margin-right: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.booking-form button {
  padding: 6px 12px;
  background-color: #3b82f6;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.booking-form button:hover {
  background-color: #2563eb;
}

.no-slots {
  color: #666;
  margin-bottom: 16px;
}

.day-btn.disabled {
  background: #f3f4f6;
  color: #999;
  cursor: not-allowed;
  border-color: #ddd;
}
</style>
