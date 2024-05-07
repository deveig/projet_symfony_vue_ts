<script lang="ts">
import axios from 'axios';
import { AxiosError } from 'axios';
import ListIngredient from './ListIngredient.vue';

export default {
  data() {
    return {
      loader: true,
      ingredientsList: [] as Array<{
        id: number;
        ingredient: string;
        quantity: number;
        unit: string;
      }>,
      error: false,
      errorMessage: '',
      url: 'http://localhost:8000/recipe',
      name: '',
      quantity: '',
      metric: ''
    };
  },

  methods: {
    async get() {
      const response = await axios({ method: 'get', url: this.url });
      if (response.status === 200) {
        return response.data;
      }
    },
    async getIngredients() {
      try {
        const ingredients = await this.get();
        if (ingredients) {
          this.ingredientsList = ingredients;
          this.loader = false;
        }
      } catch (error) {
        this.errorMessage = 'Internal Server Error, please, retry your demand.';
        this.error = true;
        this.loader = false;
      }
    },
    async save(name: string, quantity: string, metric: string) {
      const ingredient = new FormData();
      ingredient.append('ingredient', name);
      ingredient.append('quantity', quantity);
      ingredient.append('unit', metric);
      const response = await axios({
        method: 'post',
        url: this.url,
        data: ingredient
      });
      if (response.status === 200) {
        return response.data;
      }
    },
    async validateData() {
      try {
        const isNumber = new RegExp(/\d+/);
        const isString = new RegExp(/\D+/);
        const isNotEqualToZero = new RegExp(/[^0]/);
        const isNegativeNumber = new RegExp(/-\d+/);
        const newIngredientName = this.name;
        const newIngredientQuantity = this.quantity;
        const newIngredientMetric = this.metric;
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
                this.errorMessage = '';
                this.error = false;
                const message = await this.save(
                  newIngredientName,
                  newIngredientQuantity,
                  newIngredientMetric
                );
                if (message) {
                  this.$emit('saveData');
                  this.getIngredients();
                }
              } else {
                this.errorMessage = 'Metric is a short word.';
                this.error = true;
              }
            } else {
              this.errorMessage = 'Quantity is a positive number.';
              this.error = true;
            }
          } else {
            this.errorMessage = 'Name is a short word.';
            this.error = true;
          }
        } else {
          this.errorMessage = 'All fields are required.';
          this.error = true;
        }
      } catch (error) {
        const axiosError = error as AxiosError;
        const axiosErrorResponseMessage = axiosError!.response!.data! as {
          message: string;
        };
        if (axiosErrorResponseMessage.message === 'Invalid data.') {
          this.errorMessage = 'Invalid data.';
          this.error = true;
          this.loader = false;
        } else {
          this.errorMessage = 'Internal Server Error, please, retry your demand.';
          this.error = true;
          this.loader = false;
        }
      }
    },
    async delete() {
      const response = await axios({ method: 'get', url: `${this.url}/delete` });
      if (response.status === 200) {
        return response.data;
      }
    },
    async deleteData() {
      try {
        this.loader = true;
        this.errorMessage = '';
        this.error = false;
        const message = await this.delete();
        if (message) {
          this.$emit('removeData');
          this.getIngredients();
        }
      } catch (error) {
        const axiosError = error as AxiosError;
        const axiosErrorResponseMessage = axiosError!.response!.data! as {
          message: string;
        };
        if (axiosErrorResponseMessage.message === 'No ingredient to remove.') {
          this.errorMessage = 'No ingredient to remove.';
          this.error = true;
          this.loader = false;
        } else {
          this.errorMessage = 'Internal Server Error, please, retry your demand.';
          this.error = true;
          this.loader = false;
        }
      }
    }
  },
  components: {
    ListIngredient
  },

  mounted() {
    this.getIngredients();
  }
};
</script>

<template>
  <div v-if="loader" class="loader">Please wait...</div>
  <header v-if="!loader">
    <img class="picture" src="../assets/salad.jpg" alt="Salad" />
    <div>
        <h1 class="main-title">Salad</h1>
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
          <span>Servings: <span>{{ ingredientsList.length }}</span></span>
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
            <tr class="warning" v-if="error">
              <td colspan="4">{{ errorMessage }}</td>
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
