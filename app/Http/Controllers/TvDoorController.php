<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvDoorPlayer;
use App\Models\TvDoorSchedule;
use App\Models\TvDoorLayout;
use App\Models\TvDoorMedia;
use App\Models\TvDoorCategory;
use App\Models\MaxDivulgaCampaign;
use App\Models\Produto;
use App\Traits\ResolvesLoja;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TvDoorController extends Controller
{
    use ResolvesLoja;

    protected function requireLoja()
    {
        $loja = $this->resolverLoja();
        if (!$loja) {
            abort(redirect()->route('lojas.index')->with('error', 'Crie pelo menos uma loja para acessar o painel do TvDoor.'));
        }
        return $loja;
    }

    public function index()
    {
        $loja = $this->requireLoja();
        $playersCount = TvDoorPlayer::where('loja_id', $loja->id)->count();
        $mediaCount = TvDoorMedia::where('loja_id', $loja->id)->count();
        $layoutsCount = TvDoorLayout::where('loja_id', $loja->id)->count();

        return view('lojista.tvdoor.index', compact('loja', 'playersCount', 'mediaCount', 'layoutsCount'));
    }

    // --- Players Management ---
    public function players()
    {
        $loja = $this->requireLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.players.index', compact('players', 'loja'));
    }

    public function storePlayer(Request $request)
    {
        $loja = $this->requireLoja();
        $request->validate(['name' => 'required|string|max:255']);

        TvDoorPlayer::create([
            'loja_id'      => $loja->id,
            'name'         => $request->name,
            'pairing_code' => strtoupper(Str::random(6)),
            'status'       => 'pending',
        ]);

        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Player adicionado! Use o código de pareamento no dispositivo.');
    }

    public function editPlayer(TvDoorPlayer $player)
    {
        $loja = $this->requireLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.players.index', compact('players', 'loja', 'player'));
    }

    public function updatePlayer(Request $request, TvDoorPlayer $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'forced_resolution' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ]);

        $player->update([
            'name' => $request->name,
            'description' => $request->description,
            'forced_resolution' => $request->forced_resolution,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Player atualizado com sucesso.');
    }

    public function regeneratePairingCode(TvDoorPlayer $player)
    {
        $player->update([
            'pairing_code' => strtoupper(Str::random(6)),
            'device_token' => null, // Força re-pareamento
            'status' => 'pending'
        ]);

        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Nova chave de pareamento gerada com sucesso.');
    }

    public function destroyPlayer(TvDoorPlayer $player)
    {
        $player->delete();
        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Player removido.');
    }

    // --- Media Management ---
    public function media()
    {
        $loja = $this->requireLoja();
        $media = TvDoorMedia::where('loja_id', $loja->id)->with('category')->get();
        $categories = TvDoorCategory::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.media.index', compact('media', 'categories', 'loja'));
    }

    public function storeMedia(Request $request)
    {
        $loja = $this->requireLoja();
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:51200',
            'category_id' => 'nullable|exists:tv_door_categories,id',
            'duration' => 'required|integer|min:1',
        ]);

        $path = $request->file('file')->store('tvdoor/media', 'public');
        $type = str_contains($request->file('file')->getMimeType(), 'video') ? 'video' : 'image';

        TvDoorMedia::create([
            'loja_id' => $loja->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'file_path' => $path,
            'type' => $type,
            'duration' => $request->duration,
        ]);

        return redirect()->route('lojista.tvdoor.media.index')->with('success', 'Mídia enviada com sucesso.');
    }

    // --- Layout Management ---
    public function layouts()
    {
        $loja = $this->requireLoja();
        $layouts = TvDoorLayout::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.layouts.index', compact('layouts', 'loja'));
    }

    public function createLayout()
    {
        $loja = $this->requireLoja();
        return view('lojista.tvdoor.layouts.editor', compact('loja'));
    }

    public function storeLayout(Request $request)
    {
        $loja = $this->requireLoja();
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $content = $request->content;
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            }
        }

        TvDoorLayout::create([
            'loja_id'    => $loja->id,
            'name'       => $request->name,
            'duration'   => $request->duration ?? 15,
            'content'    => $content,
            'resolution' => $request->resolution ?? '1920x1080',
        ]);

        return redirect()->route('lojista.tvdoor.layouts.index')->with('success', 'Layout salvo com sucesso.');
    }

    public function editLayout(TvDoorLayout $layout)
    {
        $loja = $this->requireLoja();
        return view('lojista.tvdoor.layouts.editor', compact('loja', 'layout'));
    }

    /**
     * Busca paginada de produtos via AJAX (5 por página)
     */
    public function searchProducts(Request $request)
    {
        $loja  = $this->resolverLoja();
        $q     = $request->get('q', '');
        $page  = max(0, (int) $request->get('page', 0));
        $limit = 5;

        $query = Produto::where('loja_id', $loja->id);
        if ($q) {
            $query->where(function($sq) use ($q) {
                $sq->where('nome', 'like', "%{$q}%")
                   ->orWhere('codigo_barra', 'like', "%{$q}%");
            });
        }

        $total = $query->count();
        $produtos = $query->skip($page * $limit)->take($limit)->get()->map(function($p) use ($loja) {
            $p->imagem_url = $this->resolveProductImageUrl($p, $loja);
            return [
                'id'        => $p->id,
                'nome'      => $p->nome,
                'preco'     => number_format($p->preco ?? 0, 2, ',', '.'),
                'codigo'    => $p->codigo_barra,
                'imagem_url'=> $p->imagem_url,
            ];
        });

        return response()->json(['total' => $total, 'produtos' => array_values($produtos->toArray())]);
    }

    /**
     * View de prévia de layout (new tab)
     */
    public function previewLayout()
    {
        return view('lojista.tvdoor.layouts.preview');
    }

    /**
     * Resolve a URL da imagem do produto
     * Padrão do sistema: storage/lojas/{codigo}/produtos/{filename}
     */
    private function resolveProductImageUrl($produto, $loja)
    {
        // 1. Tenta pelo campo imagem (normalmente só o filename)
        if ($produto->imagem) {
            $filename = basename($produto->imagem);
            $byCode   = 'lojas/' . $loja->codigo . '/produtos/' . $filename;
            if (\Storage::disk('public')->exists($byCode)) {
                return asset('storage/' . $byCode);
            }
            // Se imagem tiver o path completo ou parcial
            if (\Storage::disk('public')->exists($produto->imagem)) {
                return asset('storage/' . ltrim($produto->imagem, '/'));
            }
        }
        // 2. Tenta pelo codigo_barras (padrao mais comum do MaxPDV)
        if ($produto->codigo_barras) {
            foreach (['.jpg', '.jpeg', '.png', '.webp'] as $ext) {
                $path = 'lojas/' . $loja->codigo . '/produtos/' . $produto->codigo_barras . $ext;
                if (\Storage::disk('public')->exists($path)) {
                    return asset('storage/' . $path);
                }
            }
        }
        return null;
    }

    public function updateLayout(Request $request, TvDoorLayout $layout)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $content = $request->content;
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            }
        }

        $layout->update([
            'name'       => $request->name,
            'duration'   => $request->duration ?? 15,
            'content'    => $content,
            'resolution' => $request->resolution ?? $layout->resolution,
        ]);
        return redirect()->route('lojista.tvdoor.layouts.index')->with('success', 'Layout atualizado com sucesso.');
    }

    public function destroyLayout(TvDoorLayout $layout)
    {
        $layout->delete();
        return redirect()->route('lojista.tvdoor.layouts.index')->with('success', 'Layout excluído.');
    }

    public function uploadLayoutAsset(Request $request)
    {
        $loja = $this->requireLoja();
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'asset_' . time() . '_' . Str::random(5) . '.' . $extension;
            
            // Salva em storage/app/public/tvdoor/layout_assets/{loja_id}/
            $path = $file->storeAs('tvdoor/layout_assets/' . $loja->id, $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Nenhum arquivo enviado.'], 400);
    }

    public function destroyMedia(TvDoorMedia $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        return redirect()->route('lojista.tvdoor.media.index')->with('success', 'Mídia excluída.');
    }

    public function updateMedia(Request $request, TvDoorMedia $media)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $media->update([
            'name' => $request->name,
            'duration' => $request->duration,
        ]);

        return redirect()->route('lojista.tvdoor.media.index')->with('success', 'Mídia atualizada com sucesso.');
    }

    // --- Schedule Management ---
    public function schedules()
    {
        $loja = $this->requireLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        $media = TvDoorMedia::where('loja_id', $loja->id)->get();
        $layouts = TvDoorLayout::where('loja_id', $loja->id)->get();
        $campaigns = MaxDivulgaCampaign::where('loja_id', $loja->id)->where('is_active', true)->get();
        
        // Busca agendamentos vinculados a qualquer player da loja
        $schedules = TvDoorSchedule::whereIn('player_id', $players->pluck('id'))
            ->orWhere(function($q) use ($players) {
                foreach($players as $p) {
                    $q->orWhereJsonContains('player_ids', $p->id);
                }
            })
            ->with(['player'])
            ->orderBy('priority', 'desc')
            ->get();
            
        return view('lojista.tvdoor.schedules.index', compact('players', 'media', 'layouts', 'campaigns', 'schedules', 'loja'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'player_ids'    => 'required|array',
            'player_ids.*'  => 'exists:tv_door_players,id',
            'content_items' => 'required|string',
            'time_slots'    => 'required|string',
            'priority'      => 'nullable|integer',
            'resolution'    => 'nullable|string',
        ]);

        $contentItems = json_decode($request->content_items, true) ?: [];
        $timeSlots    = json_decode($request->time_slots, true) ?: [];
        $playerIds    = array_map('intval', $request->player_ids);

        TvDoorSchedule::create([
            'player_id'        => $playerIds[0] ?? null,
            'player_ids'       => $playerIds,
            'content_items'    => $contentItems,
            'time_slots'       => $timeSlots,
            'schedulable_id'   => $contentItems[0]['id'] ?? null,
            'schedulable_type' => $contentItems[0]['type'] ?? null,
            'days'             => array_values(array_unique(array_column($timeSlots, 'day'))),
            'start_time'       => $timeSlots[0]['start'] ?? '00:00',
            'end_time'         => $timeSlots[0]['end'] ?? '23:59',
            'priority'         => $request->priority ?? 0,
            'resolution'       => $request->resolution ?? '1920x1080',
            'is_active'        => true,
        ]);

        return redirect()->route('lojista.tvdoor.schedules.index')->with('success', 'Programação criada com sucesso!');
    }

    public function editSchedule(TvDoorSchedule $schedule)
    {
        $loja = $this->requireLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        $media = TvDoorMedia::where('loja_id', $loja->id)->get();
        $layouts = TvDoorLayout::where('loja_id', $loja->id)->get();
        $campaigns = MaxDivulgaCampaign::where('loja_id', $loja->id)->where('is_active', true)->get();
        $schedules = TvDoorSchedule::whereIn('player_id', $players->pluck('id'))
            ->orderBy('priority', 'desc')->get();
        
        return view('lojista.tvdoor.schedules.index', compact('schedules', 'players', 'media', 'layouts', 'campaigns', 'loja', 'schedule'));
    }

    public function updateSchedule(Request $request, TvDoorSchedule $schedule)
    {
        $request->validate([
            'player_ids'    => 'required|array',
            'player_ids.*'  => 'exists:tv_door_players,id',
            'content_items' => 'required|string',
            'time_slots'    => 'required|string',
            'priority'      => 'nullable|integer',
            'resolution'    => 'nullable|string',
        ]);

        $contentItems = json_decode($request->content_items, true) ?: [];
        $timeSlots    = json_decode($request->time_slots, true) ?: [];
        $playerIds    = array_map('intval', $request->player_ids);

        $schedule->update([
            'player_id'        => $playerIds[0] ?? $schedule->player_id,
            'player_ids'       => $playerIds,
            'content_items'    => $contentItems,
            'time_slots'       => $timeSlots,
            'schedulable_id'   => $contentItems[0]['id'] ?? $schedule->schedulable_id,
            'schedulable_type' => $contentItems[0]['type'] ?? $schedule->schedulable_type,
            'days'             => array_values(array_unique(array_column($timeSlots, 'day'))),
            'start_time'       => $timeSlots[0]['start'] ?? $schedule->start_time,
            'end_time'         => $timeSlots[0]['end'] ?? $schedule->end_time,
            'priority'         => $request->priority ?? $schedule->priority,
            'resolution'       => $request->resolution ?? $schedule->resolution,
        ]);

        return redirect()->route('lojista.tvdoor.schedules.index')->with('success', 'Programação atualizada!');
    }

    public function destroySchedule(TvDoorSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('lojista.tvdoor.schedules.index')->with('success', 'Agendamento removido.');
    }

    // --- API Methods for Devices (Sincronização) ---

    public function checkPairingCode($code)
    {
        $player = TvDoorPlayer::where('pairing_code', strtoupper($code))
            ->where('status', 'pending')
            ->first();

        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Código inválido ou já pareado.'], 404);
        }

        $token = Str::random(64);
        $player->update([
            'device_token' => $token,
            'status' => 'online',
            'pairing_code' => null,
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'player_id' => $player->id,
            'name' => $player->name
        ]);
    }

    public function sync(Request $request)
    {
        $token = $request->header('X-Device-Token');
        $player = TvDoorPlayer::where('device_token', $token)->first();

        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        }

        $player->update(['last_seen_at' => now(), 'status' => 'online']);

        $now = now()->toTimeString();
        $day = strtolower(now()->format('D')); // mon, tue, ...

        // Busca todos os agendamentos ativos do player (suporta novo player_ids e legado player_id)
        $schedules = TvDoorSchedule::where('is_active', true)
            ->where(function($q) use ($player) {
                $q->where('player_id', $player->id)
                  ->orWhereJsonContains('player_ids', $player->id);
            })
            ->get()
            ->filter(function ($s) use ($day, $now) {
                // Novo sistema: verifica time_slots
                if (!empty($s->time_slots)) {
                    foreach ($s->time_slots as $slot) {
                        if (($slot['day'] ?? '') === $day &&
                            $now >= ($slot['start'] ?? '00:00') &&
                            $now <= ($slot['end'] ?? '23:59')) {
                            return true;
                        }
                    }
                    return false;
                }
                // Retrocompatibilidade: sistema antigo com days/start_time/end_time
                return in_array($day, $s->days ?? []) &&
                       $now >= ($s->start_time ?? '00:00') &&
                       $now <= ($s->end_time ?? '23:59');
            })
            ->sortByDesc('priority');

        $playlist = [];

        foreach ($schedules as $s) {
            // Novo sistema: usa content_items (playlist)
            $contentItems = $s->content_items ?? [];

            if (empty($contentItems)) {
                // Retrocompatibilidade: usa schedulable único
                if ($s->schedulable_id) {
                    $contentItems = [['id' => $s->schedulable_id, 'type' => $s->schedulable_type]];
                }
            }

            foreach ($contentItems as $ci) {
                $type = $ci['type'] ?? '';
                $id   = $ci['id'] ?? null;
                if (!$id) continue;

                $entry = [
                    'type' => $type,
                    'duration' => 15,
                    'schedule_id' => $s->id,
                    'mute' => $ci['mute'] ?? false
                ];

                if (str_contains($type, 'TvDoorMedia')) {
                    $media = TvDoorMedia::find($id);
                    if ($media) {
                        // file_path já inclui o caminho relativo ao storage
                        $entry['media_url']  = asset('storage/' . $media->file_path);
                        $entry['media_type'] = $media->type;
                        $entry['duration']   = $media->duration ?? 10;
                    }
                } elseif (str_contains($type, 'TvDoorLayout')) {
                    $layout = TvDoorLayout::find($id);
                    if ($layout) {
                        // Retorna o JSON completo do Fabric.js com preços atualizados
                        $entry['layout_fabric'] = $this->injectDynamicPrices($layout->content);
                        $entry['resolution']    = $layout->resolution ?? '1920x1080';
                        $entry['duration']      = $layout->duration ?? 15;
                    }
                } elseif (str_contains($type, 'MaxDivulgaCampaign')) {
                    $campaign = MaxDivulgaCampaign::find($id);
                    if ($campaign) {
                        // Corrige path duplicado: se já começa com storage/, usa asset() direto
                        $filePath  = $campaign->file_path ?? '';
                        $audioPath = $campaign->audio_file_path ?? '';

                        $entry['media_url'] = $filePath
                            ? (str_starts_with($filePath, 'storage/')
                                ? asset($filePath)
                                : asset('storage/' . $filePath))
                            : null;

                        $entry['audio_url'] = $audioPath
                            ? (str_starts_with($audioPath, 'storage/')
                                ? asset($audioPath)
                                : asset('storage/' . $audioPath))
                            : null;

                        $entry['duration'] = 15;
                    }
                }

                $playlist[] = $entry;
            }
        }

        if (empty($playlist)) {
            return response()->json(['success' => false, 'message' => 'Sem programação ativa agora.']);
        }

        return response()->json([
            'success'  => true,
            'playlist' => array_values($playlist),
            'config'   => ['sync_interval' => 60],
        ]);
    }

    /**
     * Injeta os preços atuais do banco de dados no JSON do Fabric.js
     */
    private function injectDynamicPrices($content)
    {
        if (empty($content)) return $content;

        $data = is_array($content) ? $content : json_decode($content, true);
        if (!$data) return $content;

        $isFabricWrapped = isset($data['fabric']);
        $objects = $isFabricWrapped ? ($data['fabric']['objects'] ?? []) : ($data['objects'] ?? []);

        if (empty($objects)) return $content;

        $updated = false;
        foreach ($objects as &$obj) {
            if (isset($obj['data']['productId']) && ($obj['data']['type'] ?? '') === 'price') {
                $produto = Produto::find($obj['data']['productId']);
                if ($produto) {
                    $obj['text'] = 'R$ ' . number_format($produto->preco ?? 0, 2, ',', '.');
                    $updated = true;
                }
            }
        }

        if ($updated) {
            if ($isFabricWrapped) {
                $data['fabric']['objects'] = $objects;
            } else {
                $data['objects'] = $objects;
            }
            return $data;
        }

        return $content;
    }
}
