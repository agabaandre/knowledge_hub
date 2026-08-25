import InstructorDetailsMain from "@/components/pages/page-layout-three/Instructor/Instructor-details/InstructorDetailsMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
    title: "Instructor Details - Education & Online Courses React NextJs Template",
};

interface PageProps {
    params: Promise<{ id: number }>;
}

const InstructorDetails = async (props: PageProps) => {
    const resolvedParams = await props.params;
    const { id } = resolvedParams;

    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDetailsMain id={id} />
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorDetails;
