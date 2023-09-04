import { test, expect } from 'vitest';
import { VueWrapper, mount } from '@vue/test-utils';
import ListIngredient from '../ListIngredient.vue';

test("display recipe's ingredients features in table cells", () => {
  const ingredient: {
    index: number;
    ingredient: string;
    quantity: number;
    unit: string;
  } = { index: 1, ingredient: 'salad', quantity: 1, unit: 'piece' };
  const wrapper: VueWrapper = mount(ListIngredient, {
    propsData: ingredient
  });
  expect(wrapper.element.childNodes[0].textContent).toBe(ingredient.index.toString());
  expect(wrapper.element.childNodes[1].textContent).toBe(ingredient.ingredient);
  expect(wrapper.element.childNodes[2].textContent).toBe(ingredient.quantity.toString());
  expect(wrapper.element.childNodes[3].textContent).toBe(ingredient.unit);
});