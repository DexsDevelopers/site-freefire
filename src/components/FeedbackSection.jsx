import React from 'react';
import { Star, MessageSquare } from 'lucide-react';

const reviews = [
  {
    id: 1,
    name: "Nobru",
    avatar: "N",
    rating: 5,
    text: '"Usei em live e em apostado e ninguem achou, TOP demais <3"'
  }
];

const FeedbackSection = () => {
  return (
    <div className="bg-ff-black py-16">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-left mb-12">
          <h2 className="text-3xl md:text-4xl font-black text-white uppercase tracking-wider">
            FEEDBACK DOS <span className="relative inline-block text-ff-red after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-1 after:bg-ff-red">CLIENTES</span>
          </h2>
        </div>

        <div className="grid grid-cols-1 gap-8 mb-12">
          {reviews.map((review) => (
            <div key={review.id} className="bg-[#0f0f0f] border border-white/5 p-8 rounded-2xl shadow-lg flex flex-col md:flex-row items-start md:items-center gap-6">
              <div className="flex-shrink-0">
                <div className="w-16 h-16 bg-[#1a1a1a] rounded-full flex items-center justify-center border-2 border-ff-red/50 shadow-[0_0_15px_rgba(255,0,0,0.2)]">
                  <span className="text-2xl font-black text-white">{review.avatar}</span>
                </div>
              </div>
              
              <div className="flex-grow">
                <div className="flex items-center gap-4 mb-2">
                   <h3 className="text-xl font-bold text-white">{review.name}</h3>
                   <div className="flex">
                      {[...Array(review.rating)].map((_, i) => (
                         <Star key={i} className="w-4 h-4 text-yellow-500 fill-current" />
                      ))}
                   </div>
                </div>
                <p className="text-gray-400 italic text-lg">{review.text}</p>
              </div>
            </div>
          ))}
        </div>

        <div className="flex justify-center">
           <button className="flex items-center gap-3 px-8 py-3 bg-transparent border-2 border-ff-red text-white font-bold uppercase rounded-lg hover:bg-ff-red hover:text-white transition-all duration-300 group">
              <MessageSquare className="w-5 h-5 group-hover:scale-110 transition-transform" />
              DEIXAR MINHA AVALIAÇÃO
           </button>
        </div>
      </div>
    </div>
  );
};

export default FeedbackSection;
