import { test, expect } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import ListIngredient from '../ListIngredient.vue';
import RecipePage from '../RecipePage.vue';

const sinon = require('sinon');

test('displays recipe ingredients features in table cells', () => {
  // Arranges
  const ingredient: {
    index: number;
    ingredient: string;
    quantity: number;
    unit: string;
  } = { index: 1, ingredient: 'salad', quantity: 1, unit: 'piece'};
  // Acts
  const wrapper = mount(ListIngredient, {
    propsData: ingredient
  });
  // Asserts
  expect(wrapper.element.childNodes[0].textContent).toBe(ingredient.index.toString());
  expect(wrapper.element.childNodes[1].textContent).toBe(ingredient.ingredient);
  expect(wrapper.element.childNodes[2].textContent).toBe(ingredient.quantity.toString());
  expect(wrapper.element.childNodes[3].textContent).toBe(ingredient.unit);
});
test('returns list of the ingredients', async () => {
  // Arranges
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'}
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
        user: user
      };
    }
  });
  const table = wrapper.find('tbody');
  const ingredientFeatures = table.element.children;
  const ingredientsList = [{ id: 1, ingredient: 'salad', quantity: '1', unit: 'piece', user: user }];
  const getFunction = sinon.stub(wrapper.vm, 'get');
  getFunction.returns(Promise.resolve(ingredientsList));
  // Acts
  wrapper.vm.getIngredients();
  await flushPromises();
  // Asserts
  expect(getFunction.calledOnce).toEqual(true);
  expect(ingredientFeatures[0].children[0].textContent).toBe('1');
  expect(ingredientFeatures[0].children[1].textContent).toBe(ingredientsList[0].ingredient);
  expect(ingredientFeatures[0].children[2].textContent).toBe(
    ingredientsList[0].quantity.toString()
  );
  expect(ingredientFeatures[0].children[3].textContent).toBe(ingredientsList[0].unit);
});
test('displays user', async () => {
  // Arranges
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'};
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
      };
    }
  });
  const title = wrapper.find('h1');
  const usernameTitle = title.element.children;
  const getFunction = sinon.stub(wrapper.vm, 'getUser');
  getFunction.returns(Promise.resolve(user));
  // Acts
  wrapper.vm.getDataOfUser();
  await flushPromises();
  // Asserts
  expect(getFunction.calledOnce).toEqual(true);
  expect(usernameTitle[0].textContent).toBe(user.userName);
});
test('clicks on plus button to add an user', async () => {
  // Arranges
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
      };
    }
  });
  const username = wrapper.find('#username');
  await username.setValue('sandra');
  const usernameElement = <HTMLInputElement>username.element;
  const form = wrapper.find('h1').get('form');
  const saveFunction = sinon.stub(wrapper.vm, 'saveUser');
  const message = { message: 'User is saved.' };
  saveFunction
    .withArgs(usernameElement.value)
    .resolves(message);
  // Acts
  await form.trigger('submit');
  // Asserts
  expect(saveFunction.calledOnce).toEqual(true);
  expect(wrapper.emitted()).toHaveProperty('saveDataOfUser');
});
test('clicks on plus button to add a valid ingredient', async () => {
  // Arranges
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'}
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
        user: user
      };
    }
  });
  const name = wrapper.find('#name');
  await name.setValue('oil');
  const quantity = wrapper.find('#quantity');
  await quantity.setValue('5');
  const metric = wrapper.find('#metric');
  await metric.setValue('cl');
  const nameElement = <HTMLInputElement>name.element;
  const quantityElement = <HTMLInputElement>quantity.element;
  const metricElement = <HTMLInputElement>metric.element;
  const form = wrapper.find('form');
  const saveFunction = sinon.stub(wrapper.vm, 'save');
  const message = { message: 'Data is saved.' };
  saveFunction
    .withArgs(nameElement.value, quantityElement.value, metricElement.value)
    .resolves(message);
  // Acts
  await form.trigger('submit');
  // Asserts
  expect(saveFunction.calledOnce).toEqual(true);
  expect(wrapper.emitted()).toHaveProperty('saveData');
});
test('clicks on plus button to add an invalid ingredient', async () => {
  // Arranges
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'}
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
        user: user
      };
    }
  });
  const name = wrapper.find('#name');
  await name.setValue('oil5');
  const quantity = wrapper.find('#quantity');
  await quantity.setValue('5');
  const metric = wrapper.find('#metric');
  await metric.setValue('cl');
  const message = 'Name is a short word.';
  const form = wrapper.find('form');
  // Acts
  await form.trigger('submit');
  // Asserts
  expect(wrapper.find('.warning').element.children[0].textContent).toBe(message);
});
test('clicks on minus button to delete the last ingredient', async () => {
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'}
  // Arranges
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
        user: user
      };
    }
  });
  const minusButton = wrapper.find('.less-item');
  const deleteFunction = sinon.stub(wrapper.vm, 'delete');
  const message = { message: 'Data is deleted.' };
  deleteFunction.returns(Promise.resolve(message));
  // Acts
  await minusButton.trigger('click');
  // Asserts
  expect(deleteFunction.calledOnce).toEqual(true);
  expect(wrapper.emitted()).toHaveProperty('removeData');
});
test('clicks on minus button to delete the last ingredient when no ingredient', async () => {
  // Arranges
  const user = {id: 1, userName: 'sam', password: 'G:@@9I7gf'}
  const wrapper = mount(RecipePage, {
    data() {
      return {
        loader: false,
        user: user
      };
    }
  });
  const minusButton = wrapper.find('.less-item');
  const deleteFunction = sinon.stub(wrapper.vm, 'delete');
  const message = { message: 'No ingredient to remove.' };
  const error = { response: { data: message, status: 400 } };
  deleteFunction.rejects(error);
  // Acts
  await minusButton.trigger('click');
  // Asserts
  expect(deleteFunction.calledOnce).toEqual(true);
  expect(wrapper.find('.warning').element.children[0].textContent).toBe(message.message);
});
