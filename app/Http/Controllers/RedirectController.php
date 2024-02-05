<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRedirectRequest;
use App\Http\Requests\UpdateRedirectRequest;
use App\Models\Redirect;
use App\Models\RedirectLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectController extends Controller
{   
    public function store(CreateRedirectRequest $request)
    {
        $data = $request->validated();

        $redirect = Redirect::create($data);

        return response()->json($redirect, 201);
    }

    public function index()
    {
        $redirects = Redirect::select('code', 'status', 'url', 'last_access', 'created_at', 'updated_at')->get();

        return response()->json($redirects);
    }

    public function update(UpdateRedirectRequest $request, Redirect $redirect)
    {
        $data = $request->validated();

        $redirect->update($data);

        return response()->json($redirect);
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();

        return response()->json(['message' => 'Redirect deleted successfully']);
    }

    public function show(Redirect $redirect)
    {
        // O $redirect já terá a instância correta com base no ID decodificado
        return response()->json($redirect->url);
    }

    public function redirect(Request $request, Redirect $redirect)
    {
        // Registre o acesso no RedirectLog
        $logData = [
            'redirect_id' => $redirect->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'query_params' => $request->query(),
            'access_time' => now(),
        ];
        
        Log::info('Redirect accessed', $logData);
        RedirectLog::create($logData);

        // Lógica para fusão de query params e redirecionamento
        $mergedQueryParams = array_merge($redirect->getQueryParams(), $request->query());

        // Redirecione para a URL de destino com os query params
        return redirect()->away($redirect->url . '?' . http_build_query($mergedQueryParams));

        
    }

    public function getStats(Redirect $redirect)
    {
        // Lógica para obter estatísticas de acesso
        $stats = [
            'total_access' => $redirect->logs()->count(),
            'unique_access' => $redirect->logs()->distinct('ip')->count(),
            'top_referrers' => $redirect->logs()->distinct('referer')->pluck('referer'),
            'last_10_days' => $this->getLast10DaysStats($redirect),
        ];

        // Retorne as estatísticas em formato JSON
        return response()->json($stats);
    }

    private function getLast10DaysStats(Redirect $redirect)
    {
        $last10DaysStats = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $logsOnDate = $redirect->logs()
                ->whereDate('access_time', $date)
                ->get();

            $last10DaysStats[] = [
                'date' => $date,
                'total' => $logsOnDate->count(),
                'unique' => $logsOnDate->unique('ip')->count(),
            ];
        }

        return $last10DaysStats;
    }

}