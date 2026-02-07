import React from 'react';
import { Link } from 'react-router-dom';
import { ChevronRight, Zap, Shield, Sparkles, Gauge, UserCheck, Wrench, Crown } from 'lucide-react';

const PromoSection = () => {
  return (
    <div className="bg-ff-black relative z-10 mt-12 pb-16">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
        
        {/* Roleta Banner */}
        <div className="relative rounded-3xl overflow-hidden bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-400 p-8 md:p-12 shadow-[0_0_40px_rgba(255,165,0,0.3)] transform hover:scale-[1.01] transition-transform duration-300">
          {/* Decorative Stars */}
          <Sparkles className="absolute top-4 left-4 text-white/40 w-8 h-8 animate-pulse" />
          <Sparkles className="absolute bottom-4 right-4 text-white/40 w-6 h-6 animate-pulse delay-75" />
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div className="space-y-6">
              <div className="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/30">
                <Sparkles className="w-4 h-4 text-white" />
                <span className="text-xs font-bold text-white uppercase tracking-wider">Nova Funcionalidade</span>
              </div>
              
              <div>
                <h2 className="text-4xl md:text-5xl font-black text-white mb-2">Roleta Diária</h2>
                <h3 className="text-2xl font-bold text-white/90">Gire e Ganhe Prêmios!</h3>
              </div>
              
              <p className="text-white/80 font-medium max-w-md">
                Teste sua sorte todos os dias e ganhe moedas, diamantes e itens exclusivos gratuitamente!
              </p>
              
              <Link to="/roleta" className="inline-flex bg-white text-orange-600 hover:bg-orange-50 font-bold py-3 px-8 rounded-full items-center gap-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 cursor-pointer w-fit">
                Experimentar Agora <ChevronRight className="w-5 h-5" />
              </Link>
            </div>
            
            {/* Roulette Image/Illustration */}
            <div className="relative hidden md:block">
               <div className="absolute -right-12 -top-12 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full transform rotate-12 z-20 shadow-lg border-2 border-white">
                  NOVO!
               </div>
               <div className="bg-black/80 rounded-xl p-2 shadow-2xl border border-white/10 transform rotate-2 hover:rotate-0 transition-all duration-500">
                  <img 
                    src="https://images.unsplash.com/photo-1596838132731-3301c3fd4317?q=80&w=500&auto=format&fit=crop" 
                    alt="Roleta Interface" 
                    className="rounded-lg w-full h-48 object-cover opacity-90"
                  />
                  <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                     <div className="flex gap-2">
                        <div className="w-20 h-24 bg-gray-800 rounded border border-gray-600 flex items-center justify-center">
                           <span className="text-2xl">💎</span>
                        </div>
                        <div className="w-24 h-28 bg-gray-700 rounded border-2 border-orange-500 flex items-center justify-center shadow-[0_0_15px_rgba(255,165,0,0.5)] transform -translate-y-2">
                           <span className="text-4xl">💰</span>
                        </div>
                        <div className="w-20 h-24 bg-gray-800 rounded border border-gray-600 flex items-center justify-center">
                           <span className="text-2xl">🔫</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
          </div>
        </div>

        {/* Alta Performance Section */}
        <div className="relative">
           {/* Background Mesh Effect (Simulated with radial gradients for now) */}
           <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(255,0,0,0.05)_0%,_transparent_70%)] pointer-events-none"></div>

           <div className="text-center mb-16 relative z-10">
              <h2 className="text-3xl md:text-5xl font-black uppercase tracking-wider text-white">
                 ALTA <span className="relative inline-block text-ff-red after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-1 after:bg-ff-red">PERFORMANCE</span>
              </h2>
           </div>

           <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto relative z-10">
              {/* Velocidade Card */}
              <div className="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                 <div className="mb-6 group-hover:scale-110 transition-transform duration-300">
                    <Gauge className="w-16 h-16 text-ff-red" strokeWidth={1.5} />
                 </div>
                 <h3 className="text-2xl font-black text-white uppercase tracking-wide mb-3">VELOCIDADE</h3>
                 <p className="text-gray-400 font-medium">Entrega instantânea via PIX.</p>
              </div>

              {/* Segurança Card */}
              <div className="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                 <div className="mb-6 group-hover:scale-110 transition-transform duration-300">
                    <UserCheck className="w-16 h-16 text-ff-red" strokeWidth={1.5} />
                 </div>
                 <h3 className="text-2xl font-black text-white uppercase tracking-wide mb-3">SEGURANÇA</h3>
                 <p className="text-gray-400 font-medium">Tecnologia de proteção Triple-Layer.</p>
              </div>

              {/* Suporte Card */}
              <div className="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                 <div className="mb-6 group-hover:scale-110 transition-transform duration-300">
                    <Wrench className="w-16 h-16 text-ff-red" strokeWidth={1.5} />
                 </div>
                 <h3 className="text-2xl font-black text-white uppercase tracking-wide mb-3">SUPORTE</h3>
                 <p className="text-gray-400 font-medium">Nossa equipe entra via AnyDesk.</p>
              </div>

              {/* Comunidade VIP Card */}
              <div className="bg-[#0a0a0a] border border-white/5 p-10 rounded-xl flex flex-col items-center text-center hover:bg-[#111] transition-all duration-300 group hover:-translate-y-1">
                 <div className="mb-6 group-hover:scale-110 transition-transform duration-300">
                    <Crown className="w-16 h-16 text-ff-red" strokeWidth={1.5} />
                 </div>
                 <h3 className="text-2xl font-black text-white uppercase tracking-wide mb-3">COMUNIDADE VIP</h3>
                 <p className="text-gray-400 font-medium">Acesso exclusivo ao Discord.</p>
              </div>
           </div>
        </div>

      </div>
    </div>
  );
};

export default PromoSection;
