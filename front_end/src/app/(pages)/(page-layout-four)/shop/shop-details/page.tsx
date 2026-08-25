import BookDetailsMain from "@/components/pages/page-layout-four/shop/shop-details/ShopDetailsMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
  title: "Shop Details - Education & Online Courses React NextJs Template",
};

const ShopDetails = () => {
  return (
    <>
      <Wrapper>
        <BookDetailsMain id={1} />
      </Wrapper>
    </>
  );
};

export default ShopDetails;
