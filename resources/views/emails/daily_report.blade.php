<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Health Report</title>
</head>
<body style="background-color: #f3f4f6; padding: 15px; font-family: Arial, sans-serif;">
    <div style="max-width: 700px; margin: 0 auto; background-color: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <h1 style="font-size: 24px; font-weight: bold; text-align: center; color: #2563eb;">Your Daily Health Report 📊</h1>
        
        <p style="text-align: center; color: #374151; margin-top: 8px;">Hello, <span style="font-weight: bold;">{{ $user->full_name }} </span> 👋</p>
        <p style="text-align: center; color: #6b7280;">Consistency is the key to progress! Keep pushing forward 💪</p>

        <!-- Date Section -->
        <div style="margin-top: 24px; text-align: center;">
            <span style="font-size: 12px; color: #9ca3af;">Report for</span>
            <p style="font-size: 18px; font-weight: bold; color: #1f2937;">{{ $progress['date'] }}</p>
        </div>

        <!-- Weight Section -->
        <div style="margin-top: 24px; background-color: #dbeafe; padding: 8px; border-radius: 6px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <p style="font-size: 18px; font-weight: bold; color: #1e40af;">⚖️ Current Weight: <span style="color: #374151;">{{ $progress['weight'] }} kg</span></p>
        </div>

        <!-- Calories Section -->
        <div style="margin-top: 24px; padding: 8px; background-color: #d1fae5; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h2 style="font-size: 20px; font-weight: bold; color: #065f46;">🔥 Calories Overview</h2>
            <div style="margin-top: 8px; color: #374151;">
                <p>🍽️ Consumed: <strong>{{ $progress['calories']['consumed']['value'] ?? 0 }} kcal</strong> (Target: {{ $progress['calories']['consumed']['target'] ?? 0 }} kcal)</p>
                <p>Status: <span style="font-weight: bold;">{{ $progress['calories']['consumed']['status'] ?? '' }}</span></p>
                <p style="margin-top: 8px;">🏃 Burned: <strong>{{ $progress['calories']['burned']['value'] ?? 0 }} kcal</strong> (Recommended: {{ $progress['calories']['burned']['recommended'] ?? 0 }} kcal)</p>
                <p>Status: <span style="font-weight: bold;">{{ $progress['calories']['burned']['status'] ?? '' }}</span></p>
            </div>
        </div>

        <!-- Macronutrients Section -->
        <div style="margin-top: 24px; padding: 8px; background-color: #fef3c7; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h2 style="font-size: 20px; font-weight: bold; color: #92400e;">🍏 Macronutrients Breakdown</h2>
            <div style="margin-top: 8px; color: #374151;">
                <p>🥩 Protein: <strong>{{ $progress['macronutrients']['protein'] ?? '0 g' }}</strong></p>
                <p>🍞 Carbs: <strong>{{ $progress['macronutrients']['carbs'] ?? '0 g' }}</strong></p>
                <p>🧈 Fats: <strong>{{ $progress['macronutrients']['fats'] ?? '0 g' }}</strong></p>
            </div>
        </div>

        <!-- Notes Section -->
        <div style="margin-top: 24px; padding: 8px; background-color: #f3f4f6; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h2 style="font-size: 18px; font-weight: bold; color: #374151;">📝 Notes & Motivation</h2>
            <p style="margin-top: 8px; color: #374151; font-style: italic;">"{{ $progress['notes'] }}"</p>
        </div>

        <!-- Motivational Quote -->
        <div style="margin-top: 24px; padding: 8px; background-color: #ede9fe; border-radius: 6px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <p style="font-size: 18px; font-weight: bold; color: #6b21a8;">"Small steps every day lead to big changes! Keep going 🚀"</p>
        </div>

        <!-- Footer -->
        <div style="margin-top: 24px; text-align: center; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb; padding-top: 8px;">
            &copy; {{ date('Y') }} Healthy App. Stay Healthy & Keep Moving! 💙
        </div>
    </div>
</body>
</html>
