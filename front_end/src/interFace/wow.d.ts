// wow.d.ts
declare module 'wow.js' {
    class WOW {
      constructor(options?: {
        boxClass?: string;
        animateClass?: string;
        offset?: number;
        mobile?: boolean;
        live?: boolean;
      });
      init(): void;
    }
    export default WOW;
  }