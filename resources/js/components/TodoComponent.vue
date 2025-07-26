<template>
    <div class="todo-container">
        <h1 class="todo-header">Task Manager</h1>
        <form @submit.prevent="submitTask" class="task-form">
            <div class="form-inputs">
                <input
                    required
                    placeholder="Task title"
                    v-model="form.title"
                    class="title-input"
                />

                <label>
                    <input type="checkbox" v-model="form.completed" />
                    Completed
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="action-btn">
                    {{ form.id ? "Update" : "Create" }} Task
                </button>

                <button
                    v-if="form.id"
                    @click="resetForm"
                    type="button"
                    class="cancel-btn"
                >
                    Cancel
                </button>
            </div>
        </form>

        <ul class="task-list">
            <li v-for="task in tasks" :key="task.id" class="task-item">
                <div>
                    <h2>{{ task.title }}</h2>
                    <p>Completed: {{ task.completed ? "Yes" : "No" }}</p>
                </div>

                <div class="task-actions">
                    <button class="action-btn" @click="editTask(task)">
                        Edit
                    </button>
                    <button class="delete-btn" @click="deleteTask(task.id)">
                        delete
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";

const tasks = ref([]);

const form = reactive({
    id: null,
    title: "",
    completed: false,
});

const fetchTasks = async () => {
    try {
        const response = await axios.get("/api/tasks");
        console.log("Tasks fetched successfully:", response.data);

        tasks.value = response.data;
    } catch (error) {
        console.error("Error fetching tasks:", error?.data?.error);
    }
};

const submitTask = async () => {
    try {
        if (form.id) {
            // Update existing task
            const response = await axios.put(`/api/tasks/${form.id}`, {
                title: form.title,
                completed: form.completed,
            });

            const index = tasks.value.findIndex((task) => task.id === form.id);
            if (index !== -1) {
                tasks.value[index] = response.data;
            }
        } else {
            // create new task
            const response = await axios.post("/api/tasks", {
                title: form.title,
                completed: form.completed,
            });

            tasks.value.push(response.data);
        }

        resetForm();
    } catch (error) {
        console.error("Error creating task:", error?.data?.error);
    }
};

const deleteTask = async (taskId) => {
    try {
        await axios.delete(`/api/tasks/${taskId}`);
        tasks.value = tasks.value.filter((task) => task.id !== taskId);

        resetForm();
    } catch (error) {
        console.error("Error deleting task:", error?.data?.error);
    }
};

const editTask = (task) => {
    form.id = task.id;
    form.title = task.title;
    form.completed = task.completed;
};

const resetForm = () => {
    form.id = null;
    form.title = "";
    form.completed = false;
};

onMounted(() => {
    fetchTasks();
});
</script>

<style>
.task-item {
    border: 2px solid #ccc;

    padding: 15px;

    border-radius: 15px;

    list-style-type: none;
}

.todo-container {
    width: 90vw;

    margin: 0 auto;

    max-width: 1200px;
}

.task-list {
    margin-top: 20px;
}

.todo-header {
    color: #333;

    margin-bottom: 20px;
}

.task-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.action-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.cancel-btn {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.delete-btn {
    background-color: red;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.task-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.form-inputs {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}

.form-actions {
    display: flex;
    gap: 10px;
}

.title-input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

@media screen and (min-width: 700px) {
    .task-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .task-form {
        flex-direction: row;
        align-items: center;
    }
}

@media (min-width: 1200px) {
    .task-list {
        grid-template-columns: 1fr 1fr 1fr;
    }
}
</style>
