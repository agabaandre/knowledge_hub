import { ProductsType } from '@/interFace/interFace';
import { useAppSelector } from '@/redux/hooks';

const useCart = () => {
    const cartProducts = useAppSelector((state) => state.cart.cartProducts);
    const wishlistProducts = useAppSelector((state) => state?.wishlist.cartProducts);

    // Cart quantity
    const getCartProductQuantity = () => {
        const uniqueProductId = new Set();
        cartProducts.forEach((product: ProductsType) => uniqueProductId.add(product.id));
        return uniqueProductId.size;
    };

    // Wishlist quantity
    const getWishlistQuantity = () => {
        const uniqueProductId = new Set();
        wishlistProducts?.forEach((product: ProductsType) => uniqueProductId.add(product.id));
        return uniqueProductId.size;
    };

    const getTotalPrice = () => {
        const totalPrice = cartProducts.reduce((total, product) => {
            if (typeof product.price === 'number' && product.price !== 0) {
                return total + (product.price ?? 0) * (product.quantity ?? 0);
            }
            return total;
        }, 0);
        return totalPrice;
    };

    return {
        getCartProductQuantity,
        getWishlistQuantity,
        getTotalPrice,
    };
};

export default useCart;
