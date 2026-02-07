import React from 'react';

const FaqPage = () => {
  const faqs = [
    {
      question: "Como recebo o painel?",
      answer: "Após o pagamento, basta abrir um ticket no nosso Discord entre 10h e 22h."
    },
    {
      question: "Qual a forma de pagamento?",
      answer: "Atualmente aceitamos apenas PIX, com entrega automática."
    },
    {
      question: "O painel é seguro?",
      answer: "Sim! Nosso painel é totalmente otimizado e sem riscos para seu dispositivo."
    },
    {
      question: "Quanto tempo dura a ativação?",
      answer: "Varia conforme o plano: diário, semanal ou mensal."
    }
  ];

  return (
    <div className="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 bg-black relative overflow-hidden">
      {/* Background Network Effect (CSS Simulation) */}
      <div className="absolute inset-0 z-0 opacity-40 pointer-events-none">
         {/* Dots and connecting lines simulation using radial gradients and transforms is complex. 
             Using a simplified grid/dot pattern with a red tint. */}
         <div className="absolute inset-0" 
              style={{
                backgroundImage: `radial-gradient(circle at 50% 50%, #330000 0%, #000000 100%)`
              }}>
         </div>
         {/* Animated floating particles simulation */}
         <div className="absolute top-0 left-0 w-full h-full overflow-hidden">
            {[...Array(20)].map((_, i) => (
                <div key={i} 
                     className="absolute rounded-full bg-ff-red opacity-20 animate-pulse"
                     style={{
                         top: `${Math.random() * 100}%`,
                         left: `${Math.random() * 100}%`,
                         width: `${Math.random() * 4 + 2}px`,
                         height: `${Math.random() * 4 + 2}px`,
                         animationDuration: `${Math.random() * 3 + 2}s`
                     }}
                ></div>
            ))}
             {/* Random connecting lines (simulated with thin divs) */}
             {[...Array(10)].map((_, i) => (
                <div key={`line-${i}`}
                     className="absolute bg-ff-red/10 transform origin-left"
                     style={{
                         top: `${Math.random() * 100}%`,
                         left: `${Math.random() * 100}%`,
                         width: '200px',
                         height: '1px',
                         transform: `rotate(${Math.random() * 360}deg)`,
                     }}
                ></div>
            ))}
         </div>
      </div>

      <div className="relative z-10 max-w-3xl mx-auto mt-12">
        <h1 className="text-4xl md:text-5xl font-black text-white text-center mb-16">
          Perguntas Frequentes
        </h1>

        <div className="space-y-6">
          {faqs.map((faq, index) => (
            <div 
              key={index}
              className="bg-[#050505]/80 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:border-ff-red/30 transition-all duration-300 shadow-[0_0_20px_rgba(0,0,0,0.5)]"
            >
              <h3 className="text-ff-red font-bold text-xl mb-3">
                {faq.question}
              </h3>
              <p className="text-gray-400 text-lg leading-relaxed">
                {faq.answer}
              </p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default FaqPage;
