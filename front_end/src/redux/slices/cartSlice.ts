"use client";
import { ProductsType } from "@/interFace/interFace";
import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { toast } from "sonner";
interface CartState {
  cartProducts: ProductsType[];
}

const initialState: CartState = {
  cartProducts: [],
};

export const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    cart_product: (state, { payload }: PayloadAction<ProductsType>) => {
      const productIndex = state.cartProducts.findIndex(
        (item) => item.id === payload.id
      );
      if (productIndex >= 0) {
        const toastId = toast.loading("");
        state.cartProducts[productIndex].quantity! += 1;
        toast.info("Increase Product Quantity", {
          id: toastId,
          duration: 1000,
        });
      } else {
        const tempProduct = { ...payload, quantity: 1 };
        const toastId = toast.loading("");
        state.cartProducts.push(tempProduct);
        toast.success(`${payload.title.slice(0, 15)} added to cart`, {
          id: toastId,
          duration: 1000,
        });
      }
    },

    cart_wishlist_product: (
      state,
      { payload }: PayloadAction<ProductsType>
    ) => {
      const productIndex = state.cartProducts.findIndex(
        (item) => item.id === payload.id
      );
      if (productIndex >= 0) {
        const toastId = toast.loading("");
        toast.info("Product Already Added On Cart", {
          id: toastId,
          duration: 1000,
        });
      } else {
        const tempProduct = { ...payload, quantity: payload.quantity ?? 1 };
        const toastId = toast.loading("");
        state.cartProducts.push(tempProduct);
        toast.success(`${payload.title.slice(0, 15)} added to cart`, {
          id: toastId,
          duration: 1000,
        });
      }
    },
    remove_cart_product: (state, { payload }: PayloadAction<ProductsType>) => {
      const toastId = toast.loading("");
      state.cartProducts = state.cartProducts.filter(
        (item) => item.id !== payload.id
      );
      toast.error(`Remove from your cart`, { id: toastId, duration: 1000 });
    },

    clear_cart: (state) => {
      const confirmMsg = window.confirm(
        "Are you sure deleted your all cart items ?"
      );
      if (confirmMsg) {
        state.cartProducts = [];
      }
    },

    decrease_quantity: (state, { payload }: PayloadAction<ProductsType>) => {
      const cartIndex = state.cartProducts.findIndex(
        (item) => item.id === payload.id
      );
      if (cartIndex >= 0) {
        const totalCart = state.cartProducts[cartIndex].quantity ?? 0;
        if (totalCart > 1) {
          const toastId = toast.loading("");
          state.cartProducts[cartIndex].quantity = totalCart - 1;
          toast.success("Quantity Decrease", { id: toastId, duration: 1000 });
        } else {
          const toastId = toast.loading("");
          toast.error(`Quantity cannot be less than 1`, {
            id: toastId,
            duration: 1000,
          });
        }
      }
    },
  },
});

export const {
  cart_product,
  cart_wishlist_product,
  remove_cart_product,
  clear_cart,
  decrease_quantity,
} = cartSlice.actions;

export default cartSlice.reducer;
