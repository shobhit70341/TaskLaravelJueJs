<template>
  <div class="container">
    <h1 class="my-4">Task Manager</h1>
    <input type="search" v-model="search" @keyup="searchTask" class="form-control my-2" name="search" placeholder="Search Task...">
    <div v-if="isLoading" class="my-4">Loading...</div>
    <div v-else>
      <ul class="list-group">
        <li v-for="task in tasks" :key="task.id" class="list-group-item">
          <h3 class="mb-2">{{ task.title }}</h3>
          <p class="mb-1">{{ task.description }}</p>
          <p class="mb-2">Due Date: {{ task.due_date }}</p>
          <div class="btn-group" role="group">
            <input type="radio" :id="'status'+task.id+'Open'" v-model="task.status" :value="'open'" :name="'status'+task.id" @change="updateTaskStatus(task)" class="btn-check" autocomplete="off">
            <label :for="'status'+task.id+'Open'" class="btn btn-outline-primary" :class="{ active: task.completed === 0 }">Open</label>
            <input type="radio" :id="'status'+task.id+'Completed'" v-model="task.status" :value="'completed'" :name="'status'+task.id" @change="updateTaskStatus(task)" class="btn-check" autocomplete="off">
            <label :for="'status'+task.id+'Completed'" class="btn btn-outline-success" :class="{ active: task.completed === 1 }">Completed</label>
          </div>
          <button @click="deleteTask(task.id)" class="btn btn-danger mx-2">Delete</button>
          <button @click="editTask(task)" class="btn btn-primary">Edit</button>
        </li>
      </ul>
      <form v-if="editingTask" @submit.prevent="updateTask" class="my-4">
        <div class="form-group my-2">
          <input v-model="editingTask.title" placeholder="Title" required class="form-control" />
        </div>
        <div class="form-group my-2">
          <input v-model="editingTask.description" placeholder="Description" class="form-control" />
        </div>
        <div class="form-group my-2">
          <input v-model="editingTask.due_date" type="date" required class="form-control" />
        </div>
        <div class="btn-group" role="group">
          <input type="radio" :id="'editStatus'+editingTask.id+'Open'" v-model="editingTask.status" :value="'open'" :name="'editStatus'+editingTask.id" class="btn-check" autocomplete="off">
          <label :for="'editStatus'+editingTask.id+'Open'" class="btn btn-outline-primary">Open</label>
          <input type="radio" :id="'editStatus'+editingTask.id+'Completed'" v-model="editingTask.status" :value="'completed'" :name="'editStatus'+editingTask.id" class="btn-check" autocomplete="off">
          <label :for="'editStatus'+editingTask.id+'Completed'" class="btn btn-outline-success">Completed</label>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
        <button @click="cancelEdit" class="btn btn-secondary">Cancel</button>
      </form>
      <form v-else @submit.prevent="createTask" class="my-4">
        <div class="form-group my-2">
          <input v-model="newTask.title" placeholder="Title" required class="form-control" />
        </div>
        <div class="form-group my-2">
          <input v-model="newTask.description" placeholder="Description" class="form-control" />
        </div>
        <div class="form-group my-2">
          <input v-model="newTask.due_date" type="date" required class="form-control" />
        </div>
        <button type="submit" class="btn btn-primary">Add Task</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      isLoading: false,
      tasks: [],
      newTask: {
        title: '',
        description: '',
        due_date: '',
      },
      editingTask: null,
      search : '',
    };
  },
  methods: {
      searchTask() {
      this.isLoading = true;
      const params = {
        search: this.search,
      };

      axios
        .get('/api/tasks-search', { params })
        .then((response) => {
          this.tasks = response.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.error(error);
          this.isLoading = false;
        });
    },
    getTasks() {
      this.isLoading = true;
      axios
        .get('/api/tasks')
        .then((response) => {
          this.tasks = response.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.error(error);
          this.isLoading = false;
        });
    },
    createTask() {
      axios
        .post('/api/tasks', this.newTask)
        .then((response) => {
          this.tasks.push(response.data);
          this.newTask = {
            title: '',
            description: '',
            due_date: '',
          };
        })
        .catch((error) => {
          console.error(error);
        });
    },
    deleteTask(taskId) {
      axios
        .delete(`/api/tasks/${taskId}`)
        .then(() => {
          this.tasks = this.tasks.filter((task) => task.id !== taskId);
        })
        .catch((error) => {
          console.error(error);
        });
    },
    editTask(task) {
      this.editingTask = { ...task };
    },
    updateTask() {
      axios
        .put(`/api/tasks/${this.editingTask.id}`, this.editingTask)
        .then(() => {
          const updatedIndex = this.tasks.findIndex((task) => task.id === this.editingTask.id);
          if (updatedIndex !== -1) {
            this.tasks.splice(updatedIndex, 1, { ...this.editingTask });
            this.editingTask = null;
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },
    cancelEdit() {
      this.editingTask = null;
    },
    updateTaskStatus(task) {
    this.isLoading = true;
    const formData = new FormData();
    formData.append('status', task.status);
    axios
    .post(`/api/tasks-status/${task.id}`, formData)
      .then(() => {
        this.isLoading = false;
          const updatedTaskIndex = this.tasks.findIndex((t) => t.id === task.id);
          if (updatedTaskIndex !== -1) {
            this.tasks[updatedTaskIndex].status = task.status;
          }

      })
      .catch((error) => {
        console.error(error);
        this.isLoading = false;
      });
  },

  },
  mounted() {
    this.getTasks();
  },
};
</script>
