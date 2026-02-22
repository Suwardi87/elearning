export type CourseSummary = {
    id: number;
    title: string;
};

export type ChallengeDetail = {
    id: number;
    step_number: number;
    title: string;
    instruction: string;
};

export type PageProps = {
    flash?: {
        success?: string;
    };
};
