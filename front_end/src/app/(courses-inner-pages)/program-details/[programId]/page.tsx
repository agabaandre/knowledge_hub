import ProgramDetailsMain from "@/components/courses-inner-pages/program-details/ProgramDetailsMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
    title: "Program Details - Education & Online Courses React NextJs Template",
};

interface PageProps {
    params: Promise<{ programId: number }>;
}

const ProgramDetails = async (props: PageProps) => {
    const resolvedParams = await props.params;
    const { programId } = resolvedParams;

    return (
        <>
            <Wrapper>
                <main>
                    <ProgramDetailsMain programId={programId} />
                </main>
            </Wrapper>
        </>
    );
};

export default ProgramDetails;
