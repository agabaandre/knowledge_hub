import ShopDetailsMain from "@/components/pages/page-layout-four/shop/shop-details/ShopDetailsMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
  title: "Shop Details - Education & Online Courses React NextJs Template",
};

interface PageProps {
  params: Promise<{ id: number }>;
}

const ShopDetails = async (props: PageProps) => {
  const resolvedParams = await props.params;
  const { id } = resolvedParams;

  return (
    <>
      <Wrapper>
        <ShopDetailsMain id={id} />
      </Wrapper>
    </>
  );
};

export default ShopDetails;

