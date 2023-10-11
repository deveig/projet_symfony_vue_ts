<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import ListIngredient from './ListIngredient.vue';
import { loadRouteLocation, useRoute, useRouter } from 'vue-router';

const loader = ref<boolean>(true);
const ingredientsList = ref<
  Array<{ id: number; ingredient: string; quantity: number; unit: string }>
>([]);
const validationError = ref<boolean>(false);
const validationErrorMessage = ref<string>('');
const url = 'http://localhost:8000/recipe';

async function postNewIngredient(url: string, method: string, body: FormData) {
  try {
    const response = await fetch(url, { method: method, body: body });
    if (response.ok) {
      window.location.reload();
    }
  } catch (error) {
    validationError.value = true;
    validationErrorMessage.value = 'Internal Server Error, please, retry your demand.';
  }
}

async function validateData(event: Event) {
  const isNumber = new RegExp(/\d+/);
  const isString = new RegExp(/\D+/);
  const isNotEqualToZero = new RegExp(/[^0]/);
  const isNegativeNumber = new RegExp(/-\d+/);
  const ingredient: FormData = new FormData();
  const form = event.target! as HTMLFormElement;
  const elementName = form.elements[2] as HTMLInputElement;
  const newIngredientName = elementName.value;
  const elementQuantity = form.elements[3] as HTMLInputElement;
  const newIngredientQuantity = elementQuantity.value;
  const elementMetric = form.elements[4] as HTMLInputElement;
  const newIngredientMetric = elementMetric.value;
  ingredient.append('ingredient', newIngredientName);
  ingredient.append('quantity', newIngredientQuantity);
  ingredient.append('unit', newIngredientMetric);
  // Checks content of each field.
  if (newIngredientName !== '' && newIngredientQuantity !== '' && newIngredientMetric !== '') {
    if (newIngredientName.length <= 25 && !isNumber.test(newIngredientName)) {
      if (
        !isString.test(newIngredientQuantity) &&
        isNotEqualToZero.test(newIngredientQuantity) &&
        !isNegativeNumber.test(newIngredientQuantity)
      ) {
        if (newIngredientMetric.length <= 10 && !isNumber.test(newIngredientMetric)) {
          postNewIngredient(url, 'POST', ingredient);
        } else {
          validationError.value = true;
          validationErrorMessage.value = 'Metric is a short word.';
        }
      } else {
        validationError.value = true;
        validationErrorMessage.value = 'Quantity is a positive number.';
      }
    } else {
      validationError.value = true;
      validationErrorMessage.value = 'Name is a short word.';
    }
  } else {
    validationError.value = true;
    validationErrorMessage.value = 'All fields are required.';
  }
}

async function getIngredients() {
  try {
    const response = await fetch(url);
    if (response.ok) {
      ingredientsList.value = await response.json();
      loader.value = false;
    }
  } catch (error) {
    validationError.value = true;
    validationErrorMessage.value = 'Internal Server Error, please, retry your demand.';
  }
}

getIngredients();
</script>

<template>
  <div v-if="loader" class="loader">Please wait.</div>
  <header v-if="!loader">
    <img class="picture" src="../assets/salad.jpg" alt="Salad" />
    <h1 class="main-title">Salad</h1>
    <p class="description">Delicious flavored salad !</p>
  </header>
  <main v-if="!loader">
    <section>
      <h2 class="subtitle">Overview</h2>
      <dl class="features">
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
        <dt class="feature-picture feature-picture-decoration">7€</dt>
        <dd class="feature">Cost</dd>
        <dt class="feature-picture feature-picture-decoration">45min</dt>
        <dd class="feature">Preparation time</dd>
        <dt class="feature-picture feature-picture-decoration">0min</dt>
        <dd class="feature">Cooking time</dd>
        <dt class="feature-picture feature-picture-decoration">20min</dt>
        <dd class="feature">Resting time</dd>
      </dl>
    </section>
    <section>
      <h2 class="subtitle">Ingredients</h2>
      <form method="post" action="" @submit.prevent="validateData">
        <div class="item-handler">
          Servings:
          <button type="button" class="less-item" name="minus" value="minus">-</button>
          <span>{{ ingredientsList.length }}</span>
          <button type="submit" class="more-item" name="plus" value="plus">+</button>
        </div>
        <table>
          <caption class="table-legend">
            List of the recipe ingredients. Fill fields and click on plus button to add ingredient
            to your recipe ! Click on minus button to remove it !
          </caption>
          <thead>
            <tr>
              <th>#</th>
              <th><label for="name">Name</label></th>
              <th><label for="quantity">Quantity</label></th>
              <th><label for="metric">Metric</label></th>
            </tr>
            <tr>
              <td></td>
              <td>
                <input id="name" name="name" required />
              </td>
              <td>
                <input id="quantity" name="quantity" required />
              </td>
              <td>
                <input id="metric" name="metric" required />
              </td>
            </tr>
            <tr class="warning" v-if="validationError">
              <td colspan="4">{{ validationErrorMessage }}</td>
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
