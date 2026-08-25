//@refresh
import HomeMain from "@/components/home/HomeMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Home - Education & Online Courses React NextJs Template",
};

const Home = () => {
  return (
    <>
        <Wrapper>
          <main className="main-area">
            <HomeMain />
          </main>
        </Wrapper>
    </>
  );
}

export default Home