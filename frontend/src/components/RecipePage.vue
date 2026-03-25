<script lang="ts">
import axios from 'axios';
import { AxiosError } from 'axios';
import ListIngredient from './ListIngredient.vue';

export default {
  data() {
    return {
      loader: true,
      user: null as { id: Number; userName: string; password: string } | null,
      username: '',
      ingredientsList: [] as Array<{
        id: number;
        ingredient: string;
        quantity: number;
        unit: string;
        user: {
          id: number;
          userName: string;
          password: string;
        };
      }>,
      errorIngredient: false,
      errorIngredientMessage: '',
      errorUser: false,
      errorUserMessage: '',rror: false,
      errorMessage: '',
      url: '/recipe-symfony-vue/recipe',
      name: '',
      quantity: '',
      metric: ''
    };
  },

  methods: {
    async saveUser(username: string) {
      let user = new FormData();
      user.append('username', username);
      let response = await axios({
        method: 'post',
        url: `${this.url}/user`,
        data: user,
        withCredentials: true
      });
      if (response.status === 201) {
        return response.data;
      }
    },
    async validateUser() {
      try {
        let isNumber = new RegExp(/\d+/);
        let newUserName: string = this.username;
        // Checks content of field.
        if (newUserName !== '' && newUserName.length <= 25 && !isNumber.test(newUserName)) {
          this.loader = true;
          this.errorUserMessage = '';
          this.errorUser = false;
          let message = await this.saveUser(newUserName);
          if (message) {
            this.$emit('saveDataOfUser');
            this.getDataOfUser();
          }
        } else {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
        }
      } catch (error) {
        let axiosError = error as AxiosError;
        let axiosErrorResponseMessage = axiosError!.response!.data as {
          message: string;
        };
        if (axiosErrorResponseMessage.message === 'Invalid data.') {
          this.errorUserMessage = 'Invalid data.';
          this.errorUser = true;
          this.loader = false;
        } else {
          this.errorUserMessage = 'Internal Server Error, please, retry your demand.';
          this.errorUser = true;
          this.loader = false;
        }
      }
    },
    async getUser() {
      let response = await axios({
        method: 'get',
        url: `${this.url}/user`,
        withCredentials: true
      });
      if (response.status === 200) {
        return response.data;
      }
    },
    async getDataOfUser() {
      try {
        const user = await this.getUser();
        if (user != null && user.id) { 
          this.user = user;
          this.username = user.userName;
          this.loader = false;
          this.getIngredients();
        } else {
          this.loader = false;
        }
      } catch (error) {
        let axiosError = error as AxiosError<{ message: string }, void>;
        let axiosErrorResponseMessage = axiosError.response?.data.message;
        if (axiosErrorResponseMessage === 'No user') {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
          this.loader = false;
        } else {
          this.errorUserMessage = 'Internal Server Error, please, retry your demand.';
          this.errorUser = true;
          this.loader = false;
        }
      }
    },
    async get() {
      let response = await axios({ method: 'get', url: this.url, withCredentials: true });
      if (response.status === 200) {
        return response.data;
      }
    },
    async getIngredients() {
      try {
        if (this.user != null) {
          let ingredients = await this.get();
          if (ingredients) {
            this.ingredientsList = ingredients;
            this.loader = false;
          } else {
            this.errorIngredientMessage = 'Internal Server Error, please, retry your demand.';
            this.errorIngredient = true;
            this.loader = false;
          }
        } else {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
          this.loader = false;
        }
      } catch (error) {
        let axiosError = error as AxiosError<{ message: string }, void>;
        let axiosErrorResponseMessage = axiosError.response?.data.message;
        if (axiosErrorResponseMessage === 'No user') {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
          this.loader = false;
        } else {
          this.errorIngredientMessage = 'Internal Server Error, please, retry your demand.';
          this.errorIngredient = true;
          this.loader = false;
        }
      }
    },
    async save(name: string, quantity: string, metric: string) {
      let ingredient = new FormData();
      ingredient.append('ingredient', name);
      ingredient.append('quantity', quantity);
      ingredient.append('unit', metric);
      let response = await axios({
        method: 'post',
        url: this.url,
        data: ingredient,
        withCredentials: true
      });
      if (response.status === 201) {
        return response.data;
      }
    },
    async validateData() {
      try {
        if (this.user != null) {
          let isNumber = new RegExp(/\d+/);
          let isString = new RegExp(/\D+/);
          let isNotEqualToZero = new RegExp(/[^0]/);
          let isNegativeNumber = new RegExp(/-\d+/);
          let newIngredientName = this.name;
          let newIngredientQuantity = this.quantity;
          let newIngredientMetric = this.metric;
          // Checks content of each field.
          if (
            newIngredientName !== '' &&
            newIngredientQuantity !== '' &&
            newIngredientMetric !== ''
          ) {
            if (newIngredientName.length <= 25 && !isNumber.test(newIngredientName)) {
              if (
                !isString.test(newIngredientQuantity) &&
                isNotEqualToZero.test(newIngredientQuantity) &&
                !isNegativeNumber.test(newIngredientQuantity)
              ) {
                if (newIngredientMetric.length <= 10 && !isNumber.test(newIngredientMetric)) {
                  this.loader = true;
                  this.errorIngredientMessage = '';
                  this.errorIngredient = false;
                  let message = await this.save(
                    newIngredientName,
                    newIngredientQuantity,
                    newIngredientMetric
                  );
                  if (message) {
                    this.$emit('saveData');
                    this.name = "";
                    this.quantity = "";
                    this.metric = "";
                    this.getIngredients();
                  }
                } else {
                  this.errorIngredientMessage = 'Metric is a short word.';
                  this.errorIngredient = true;
                }
              } else {
                this.errorIngredientMessage = 'Quantity is a positive number.';
                this.errorIngredient = true;
              }
            } else {
              this.errorIngredientMessage = 'Name is a short word.';
              this.errorIngredient = true;
            }
          } else {
            this.errorIngredientMessage = 'All fields are required.';
            this.errorIngredient = true;
          }
        } else {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
        }
      } catch (error) {
        let axiosError = error as AxiosError;
        let axiosErrorResponseMessage = axiosError!.response!.data as {
          message: string;
        };
        if (axiosErrorResponseMessage.message === 'Invalid data.') {
          this.errorIngredientMessage = 'Invalid data.';
          this.errorIngredient = true;
          this.loader = false;
        } else if (axiosErrorResponseMessage.message === 'No user') {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
          this.loader = false;
        } else {
          this.errorIngredientMessage = 'Internal Server Error, please, retry your demand.';
          this.errorIngredient = true;
          this.loader = false;
        }
      }
    },
    async delete() {
      let response = await axios({
        method: 'get',
        url: `${this.url}/delete`,
        withCredentials: true
      });
      if (response.status === 200) {
        return response.data;
      }
    },
    async deleteData() {
      try {
        if (this.user != null) {
          this.loader = true;
          this.errorIngredientMessage = '';
          this.errorIngredient = false;
          let message = await this.delete();
          if (message) {
            this.$emit('removeData');
            this.getIngredients();
          }
        } else {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
        }
      } catch (error) {
        let axiosError = error as AxiosError;
        const axiosErrorResponseMessage = axiosError!.response!.data as {
          message: string;
        };
        if (axiosErrorResponseMessage.message === 'No ingredient to remove.') {
          this.errorIngredientMessage = 'No ingredient to remove.';
          this.errorIngredient = true;
          this.loader = false;
        } else if (axiosErrorResponseMessage.message === 'No user') {
          this.errorUserMessage = 'Enter your name!';
          this.errorUser = true;
          this.loader = false;
        } else {
          this.errorIngredientMessage = 'Internal Server Error, please, retry your demand.';
          this.errorIngredient = true;
          this.loader = false;
        }
      }
    }
  },
  components: {
    ListIngredient
  },
  mounted() {
    this.getDataOfUser();
  }
};
</script>

<template>
  <div v-if="loader" class="loader">Please wait...</div>
  <header v-if="!loader">
    <img class="picture" src="../assets/salad.jpg" alt="Salad" />
    <div>
      <h1 class="main-title">
        <form
          v-if="user == null"
          @submit.prevent="validateUser"
          method="post"
          action=""
          class="form-title"
        >
          <table>
            <thead>
              <tr>
                <th class="item-datas">
                  <label for="username">Your name</label>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="item-datas">
                  <input id="username" name="username" v-model="username" />
                </td>
                <td>
                  <button type="submit" class="more-item" name="plus_name" value="plus_name">
                    +
                  </button>
                </td>
              </tr>
              <tr class="warning" v-if="errorUser">
                <td>{{ errorUserMessage }}</td>
              </tr>
            </tbody>
          </table>
        </form>
        <span v-else>{{ user.userName }}</span>
        's salad
      </h1>
      <p class="description">Delicious flavored salad !</p>
    </div>
  </header>
  <main v-if="!loader">
    <section>
      <h2 class="subtitle">Overview</h2>
      <dl class="features">
        <div>
          <dt class="feature-picture">
            <div class="rate">
              <i class="fa-solid fa-star fa-2xs"></i>
              <i class="fa-solid fa-star fa-2xs"></i>
              <i class="fa-solid fa-star-half-stroke fa-2xs"></i>
              <i class="fa-regular fa-star fa-2xs"></i>
              <i class="fa-regular fa-star fa-2xs"></i>
            </div>
          </dt>
          <dd class="feature">Difficulty</dd>
        </div>
        <div>
          <dt class="feature-picture feature-picture-decoration">7€</dt>
          <dd class="feature">Cost</dd>
        </div>
        <div>
          <dt class="feature-picture feature-picture-decoration">45min</dt>
          <dd class="feature">Preparation time</dd>
        </div>
        <div>
          <dt class="feature-picture feature-picture-decoration">0min</dt>
          <dd class="feature">Cooking time</dd>
        </div>
        <div>
          <dt class="feature-picture feature-picture-decoration">20min</dt>
          <dd class="feature">Resting time</dd>
        </div>
      </dl>
    </section>
    <section>
      <h2 class="subtitle">Ingredients</h2>
      <form method="post" action="" @submit.prevent="validateData">
        <div class="item-handler">
          <span
            >Servings: <span>{{ ingredientsList.length }}</span></span
          >
          <div>
            <button type="submit" class="more-item" name="plus" value="plus">+</button>
            <button type="button" class="less-item" name="minus" value="minus" @click="deleteData">
              -
            </button>
          </div>
        </div>
        <table>
          <caption class="table-legend">
            List of the recipe ingredients. Fill fields and click on plus button to add ingredient
            to your recipe ! Click on minus button to remove it !
          </caption>
          <thead>
            <tr>
              <th class="item-datas item-number">N°</th>
              <th class="item-datas"><label for="name">Name</label></th>
              <th class="item-datas"><label for="quantity">Quantity</label></th>
              <th class="item-datas"><label for="metric">Metric</label></th>
            </tr>
            <tr>
              <td></td>
              <td class="item-datas">
                <input id="name" name="name" required v-model="name" />
              </td>
              <td class="item-datas">
                <input id="quantity" name="quantity" required v-model="quantity" />
              </td>
              <td class="item-datas">
                <input id="metric" name="metric" required v-model="metric" />
              </td>
            </tr>
            <tr class="warning" v-if="errorIngredient">
              <td colspan="4">{{ errorIngredientMessage }}</td>
            </tr>
          </thead>
          <tbody>
            <ListIngredient
              v-for="(ingredient, index) in ingredientsList"
              :ingredient="ingredient.ingredient"
              :quantity="ingredient.quantity"
              :unit="ingredient.unit"
              :index="index + 1"
              :key="ingredient.id"
            />
          </tbody>
        </table>
      </form>
    </section>
  </main>
</template>
