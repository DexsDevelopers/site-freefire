import React from 'react';
import { Shield, AlertTriangle, Clock, Ban, Lock, CheckCircle } from 'lucide-react';

const TermsPage = () => {
  const terms = [
    {
      id: 1,
      title: "ENVIO DO PRODUTO",
      content: [
        "O envio é digital e feito automaticamente ou dentro do prazo informado no momento da compra.",
        "Produtos digitais não possuem entrega física."
      ],
      icon: <CheckCircle className="w-6 h-6 text-ff-red" />
    },
    {
      id: 2,
      title: "POLÍTICA DE REEMBOLSO",
      content: [
        "Não há reembolso para produtos digitais sob nenhuma hipótese.",
        "Após o uso, ativação, download ou execução, o cliente perde qualquer direito de contestação.",
        "Solicitações fora do horário de suporte não serão atendidas."
      ],
      icon: <Ban className="w-6 h-6 text-ff-red" />
    },
    {
      id: 3,
      title: "TAXA DE RESET / REATIVAÇÃO",
      content: [
        "Alterações de máquina, hardware ou necessidade de reset possuem taxa fixa de R$ 15,00.",
        "O reset só é realizado mediante pagamento confirmado."
      ],
      icon: <AlertTriangle className="w-6 h-6 text-ff-red" />
    },
    {
      id: 4,
      title: "RESPONSABILIDADE DE USO",
      content: [
        "Não nos responsabilizamos por banimentos ou consequências causadas por uso incorreto.",
        "Modificações externas aumentam o risco e são de total responsabilidade do cliente."
      ],
      icon: <Shield className="w-6 h-6 text-ff-red" />
    },
    {
      id: 5,
      title: "HORÁRIO DE SUPORTE",
      content: [
        "O suporte funciona diariamente entre 10h e 22h.",
        "Mensagens enviadas fora do horário serão respondidas no próximo período de suporte."
      ],
      icon: <Clock className="w-6 h-6 text-ff-red" />
    },
    {
      id: 6,
      title: "FRAUDES, GOLPES E MÁ-FÉ",
      content: [
        "Tentativas de golpe, estorno indevido ou fraude resultam em banimento permanente.",
        "Incluindo impossibilidade de futuras compras."
      ],
      icon: <Ban className="w-6 h-6 text-ff-red" />
    },
    {
      id: 7,
      title: "PRIVACIDADE E SEGURANÇA",
      content: [
        "Dados fornecidos são utilizados apenas para processamento do pedido.",
        "Informações falsas, tentativas de invasão ou compartilhamento resultam em banimento."
      ],
      icon: <Lock className="w-6 h-6 text-ff-red" />
    },
    {
      id: 8,
      title: "ACEITE DOS TERMOS",
      content: [
        "Ao comprar, o cliente declara ter lido, entendido e aceitado todos os termos acima."
      ],
      icon: <CheckCircle className="w-6 h-6 text-ff-red" />
    }
  ];

  return (
    <div className="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8 bg-ff-black">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-4xl md:text-5xl font-black text-white text-center mb-12 uppercase tracking-tighter">
          Termos de <span className="text-ff-red text-glow">Compra e Uso</span>
        </h1>

        <div className="space-y-6">
          {terms.map((term) => (
            <div key={term.id} className="bg-[#0f0f0f] border border-white/5 rounded-xl p-6 hover:border-ff-red/30 transition-all duration-300">
              <div className="flex items-center gap-4 mb-4 border-b border-white/5 pb-4">
                <div className="bg-ff-red/10 p-2 rounded-lg">
                  {term.icon}
                </div>
                <h2 className="text-xl font-bold text-ff-red uppercase tracking-wider">
                  {term.id}. {term.title}
                </h2>
              </div>
              <ul className="space-y-2">
                {term.content.map((line, index) => (
                  <li key={index} className="text-gray-400 flex items-start gap-2">
                    <span className="text-ff-red mt-1.5">•</span>
                    <span>{line}</span>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default TermsPage;
