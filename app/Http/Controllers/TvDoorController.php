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

    public function index()
    {
        $loja = $this->resolverLoja();
        $playersCount = TvDoorPlayer::where('loja_id', $loja->id)->count();
        $mediaCount = TvDoorMedia::where('loja_id', $loja->id)->count();
        $layoutsCount = TvDoorLayout::where('loja_id', $loja->id)->count();
        
        return view('lojista.tvdoor.index', compact('loja', 'playersCount', 'mediaCount', 'layoutsCount'));
    }

    // --- Players Management ---
    public function players()
    {
        $loja = $this->resolverLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.players.index', compact('players', 'loja'));
    }

    public function storePlayer(Request $request)
    {
        $loja = $this->resolverLoja();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        TvDoorPlayer::create([
            'loja_id' => $loja->id,
            'name' => $request->name,
            'pairing_code' => strtoupper(Str::random(6)),
            'status' => 'pending',
        ]);

        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Player adicionado! Use o código de pareamento no dispositivo.');
    }

    public function destroyPlayer(TvDoorPlayer $player)
    {
        $player->delete();
        return redirect()->route('lojista.tvdoor.players.index')->with('success', 'Player removido.');
    }

    // --- Media Management ---
    public function media()
    {
        $loja = $this->resolverLoja();
        $media = TvDoorMedia::where('loja_id', $loja->id)->with('category')->get();
        $categories = TvDoorCategory::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.media.index', compact('media', 'categories', 'loja'));
    }

    public function storeMedia(Request $request)
    {
        $loja = $this->resolverLoja();
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:51200', // 50MB
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
        $loja = $this->resolverLoja();
        $layouts = TvDoorLayout::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.layouts.index', compact('layouts', 'loja'));
    }

    public function createLayout()
    {
        $loja = $this->resolverLoja();
        $produtos = Produto::where('loja_id', $loja->id)->get();
        return view('lojista.tvdoor.layouts.editor', compact('produtos', 'loja'));
    }

    public function storeLayout(Request $request)
    {
        $loja = $this->resolverLoja();
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|json',
        ]);

        TvDoorLayout::create([
            'loja_id' => $loja->id,
            'name' => $request->name,
            'content' => json_decode($request->content, true),
        ]);

        return redirect()->route('lojista.tvdoor.layouts.index')->with('success', 'Layout salvo com sucesso.');
    }

    // --- Schedule Management ---
    public function schedules()
    {
        $loja = $this->resolverLoja();
        $players = TvDoorPlayer::where('loja_id', $loja->id)->get();
        $media = TvDoorMedia::where('loja_id', $loja->id)->get();
        $layouts = TvDoorLayout::where('loja_id', $loja->id)->get();
        $campaigns = MaxDivulgaCampaign::where('loja_id', $loja->id)->where('is_active', true)->get();
        $schedules = TvDoorSchedule::whereIn('player_id', $players->pluck('id'))->with(['player', 'schedulable'])->orderBy('priority', 'desc')->get();

        return view('lojista.tvdoor.schedules.index', compact('players', 'media', 'layouts', 'campaigns', 'schedules', 'loja'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:tv_door_players,id',
            'schedulable_id' => 'required|integer',
            'schedulable_type' => 'required|string',
            'days' => 'required|array',
            'start_time' => 'required',
            'end_time' => 'required',
            'priority' => 'nullable|integer',
        ]);

        TvDoorSchedule::create($request->all());

        return redirect()->route('lojista.tvdoor.schedules.index')->with('success', 'Agendamento criado!');
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
        $day = strtolower(date('D'));

        $schedules = TvDoorSchedule::with('schedulable')
            ->where('player_id', $player->id)
            ->where('is_active', true)
            ->get()
            ->filter(function ($s) use ($day, $now) {
                return in_array($day, $s->days) && ($now >= $s->start_time && $now <= $s->end_time);
            })
            ->sortByDesc('priority');

        $playlist = $schedules->map(function ($s) {
            $item = [
                'id' => $s->id,
                'type' => $s->schedulable_type,
                'duration' => 15, // default
            ];

            if ($s->schedulable_type === TvDoorMedia::class) {
                $item['media_url'] = asset('storage/' . $s->schedulable->file_path);
                $item['media_type'] = $s->schedulable->type;
                $item['duration'] = $s->schedulable->duration;
            } elseif ($s->schedulable_type === TvDoorLayout::class) {
                $item['layout_content'] = $s->schedulable->content;
            } elseif ($s->schedulable_type === MaxDivulgaCampaign::class) {
                $item['media_url'] = $s->schedulable->file_path ? asset('storage/' . $s->schedulable->file_path) : null;
                $item['audio_url'] = $s->schedulable->audio_file_path ? asset('storage/' . $s->schedulable->audio_file_path) : null;
            }

            return $item;
        })->values();

        return response()->json([
            'success' => true,
            'playlist' => $playlist,
            'config' => [
                'sync_interval' => 60,
            ]
        ]);
    }
}
