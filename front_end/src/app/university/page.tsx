//@refresh
import UniversityMain from "@/components/university/UniversityMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "University - Education & Online Courses React NextJs Template",
};

const University = () => {
  return (
    <>
      <Wrapper>
        <main className="main-area">
          <UniversityMain />
        </main>
      </Wrapper>
    </>
  );
}

export default University;