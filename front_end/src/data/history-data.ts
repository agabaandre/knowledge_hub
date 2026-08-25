import { TimelineData } from '@/interFace/interFace';
import historyImgOne from '../../public/assets/images/history/history-img-4.webp';
import historyImgTwo from '../../public/assets/images/history/history-img-5.webp';
import historyImgThree from '../../public/assets/images/history/history-img-6.webp';
import historyImgFour from '../../public/assets/images/history/history-img-7.webp';
import historyImgFive from '../../public/assets/images/history/history-img-8.webp';

export const timelineData: TimelineData[] = [
    {
      id: "1600s",
      title: "1600s",
      description: "iStudy University was founded in the late 1600s with a vision to become a beacon of knowledge in the region. The institution was established to provide a platform for higher learning, driven by a commitment to academic excellence and societal advancement.",
      image: historyImgOne,
      events: [
        { year: "1636", description: "iStudy University is founded as one of the first higher education institutions in the region, with a mission to promote knowledge and leadership." },
        { year: "1640", description: "The first class of students enrolls, marking the start of an enduring tradition of academic excellence." },
      ],
    },
    {
      id: "1700s",
      title: "1700s",
      description: "Throughout the 1700s, iStudy played a significant role in shaping the educational landscape, serving as a model for other institutions. The university's emphasis on classical education, combined with its focus on leadership development, positioned it as a key player in the region's intellectual growth.",
      image: historyImgTwo,
      events: [
        { year: "1721", description: "iStudy introduces its first endowed chair, helping to elevate its reputation for specialized education." },
        { year: "1776", description: "During the American Revolution, iStudy becomes a center for intellectual thought and leadership, influencing key figures of the era." },
      ],
    },
    {
      id: "1800s",
      title: "1800s",
      description: "The 1800s marked a period of significant growth for iStudy. New faculties were established, and the university expanded its reach through the creation of research programs and facilities. This era laid the groundwork for the institution's transformation into a global center of learning.",
      image: historyImgThree,
      events: [
        { year: "1812", description: "iStudy establishes its first research center, focusing on science and engineering, which marks a shift toward research-based education." },
        { year: "1850", description: "The university opens new faculties, expanding its offerings to include law, medicine, and the humanities." },
        { year: "1881", description: "Construction of the new campus library, one of the largest academic libraries of its time." },
      ],
    },
    {
      id: "1900s",
      title: "1900s",
      description: "In the 1900s, iStudy University underwent modernization, adapting to the rapidly changing world. The university expanded its influence on a global scale, attracting international students and becoming a leader in research, innovation, and societal progress.",
      image: historyImgFour,
      events: [
        { year: "1920", description: "iStudy welcomes its first international students, paving the way for a more diverse academic community." },
        { year: "1955", description: "The university launches its first graduate program, further cementing its role as a leader in research and higher education." },
        { year: "1999", description: "iStudy becomes a global institution, partnering with universities around the world for exchange programs and collaborative research." },
      ],
    },
    {
      id: "2000s",
      title: "2000s",
      description: "Entering the 21st century, iStudy University embraced a new era of breakthroughs in technology, science, and the arts. The institution continues to foster innovation and discovery, remaining at the forefront of educational excellence in an ever-evolving world.",
      image: historyImgFive,
      events: [
        { year: "2001", description: "iStudy University integrates cutting-edge technology into its curriculum, leading innovations in online education and digital learning platforms." },
        { year: "2015", description: "The university reaches a milestone with over 50,000 alumni worldwide, many of whom are leaders in their respective fields." },
        { year: "2024", description: "iStudy continues to push boundaries with advancements in artificial intelligence, sustainability, and global health." },
      ],
    },
  ];