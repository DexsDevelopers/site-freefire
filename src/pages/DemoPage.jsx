import React from 'react';
import { Play, Lock } from 'lucide-react';

const DemoPage = () => {
  return (
    <div className="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 bg-ff-black">
      <div className="max-w-6xl mx-auto">
        <h1 className="text-4xl md:text-5xl font-black text-white text-center mb-12 uppercase tracking-tighter">
          Demonstração do <span className="text-ff-red text-glow">Painel</span>
        </h1>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          {/* Video Placeholder */}
          <div className="relative aspect-video bg-black rounded-xl border border-white/10 overflow-hidden shadow-2xl group cursor-pointer">
            <div className="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition-all">
              <div className="w-20 h-20 bg-ff-red/90 rounded-full flex items-center justify-center pl-1 shadow-[0_0_30px_rgba(255,0,0,0.4)] group-hover:scale-110 transition-transform">
                <Play className="w-8 h-8 text-white fill-white" />
              </div>
            </div>
            <img 
              src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1200&auto=format&fit=crop" 
              alt="Demo Preview" 
              className="w-full h-full object-cover opacity-60"
            />
            
            <div className="absolute bottom-4 left-4 bg-black/80 px-3 py-1 rounded text-xs font-mono text-white border border-white/10">
              PREVIEW_V2.mp4
            </div>
          </div>

          {/* Features List */}
          <div className="space-y-8">
            <div>
              <h2 className="text-3xl font-bold text-white mb-4 uppercase">Interface Intuitiva</h2>
              <p className="text-gray-400 leading-relaxed">
                Nosso painel foi desenhado para ser simples e eficiente. Com poucos cliques você configura todas as funções do cheat, sem necessidade de conhecimentos técnicos avançados.
              </p>
            </div>

            <div className="space-y-4">
              <div className="bg-[#0f0f0f] p-4 rounded-lg border border-white/5 flex items-start gap-4">
                <div className="bg-ff-red/10 p-2 rounded">
                  <Lock className="w-6 h-6 text-ff-red" />
                </div>
                <div>
                  <h3 className="font-bold text-white mb-1">Proteção HWID</h3>
                  <p className="text-sm text-gray-500">Sistema único de proteção que garante a segurança da sua máquina.</p>
                </div>
              </div>
              
              <div className="bg-[#0f0f0f] p-4 rounded-lg border border-white/5 flex items-start gap-4">
                <div className="bg-ff-red/10 p-2 rounded">
                  <Play className="w-6 h-6 text-ff-red" />
                </div>
                <div>
                  <h3 className="font-bold text-white mb-1">Injeção Rápida</h3>
                  <p className="text-sm text-gray-500">Carregamento instantâneo sem afetar o FPS do seu jogo.</p>
                </div>
              </div>
            </div>

            <button className="bg-ff-red hover:bg-ff-dark-red text-white font-black uppercase tracking-wider py-4 px-8 rounded-lg w-full md:w-auto shadow-[0_0_20px_rgba(255,0,0,0.3)] transition-all hover:scale-105">
              Quero Adquirir Agora
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DemoPage;
